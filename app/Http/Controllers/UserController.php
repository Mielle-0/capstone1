<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Charts\FeedbackChart;
use App\Charts\TopDepartmentsChart;
use App\Charts\VolumeChart;
use App\Charts\ClassificationChart;
use App\Charts\DepartmentChart;
use App\Charts\ConfidenceChart;
use App\Models\User;
use App\Models\Feedback;
use App\Models\AiSetting;
use App\Models\FeedbackPrediction;
use App\Models\Ticket;
use App\Models\Action;
use App\Models\Role;
use App\Models\TicketReturn;

class UserController extends Controller
{

    public function dashboard(Request $request)
    {
        $user = auth()->user();
        
        // We will store all variables in this array to pass to the view
        $data = [];

        // 2. Super Admin Data (AI & Users)
        if ($user->hasAnyRole(['Super Admin'])) {
            $data['totalUsers'] = User::count();
            $data['activeUsers'] = User::where('usr_active', 1)->count();
            $data['inactiveUsers'] = $data['totalUsers'] - $data['activeUsers'];

            // Get recently added users (assuming higher usr_id means newer)
            $data['recentUsers'] = User::with('roles')
                ->orderBy('usr_id', 'desc')
                ->take(5)
                ->get();

            
            $data['roleDistribution'] = Role::withCount(['users' => function($query) {
                    $query->where('usr_active', 1);
                }])
                ->where('rol_active', 1)
                ->get();

            
            // Date Range Filter
            // Capture Dates from the Request (Default to last 30 days if null)
            $fromDateStr = $request->input('ml_date_from', now()->subDays(30)->format('Y-m-d'));
            $toDateStr   = $request->input('ml_date_to', now()->format('Y-m-d'));
            
            // Append times to capture the full start and end days
            $fromDate = $fromDateStr . ' 00:00:00';
            $toDate   = $toDateStr . ' 23:59:59';

            $data['currentAiVersion'] = AiSetting::get('active_model_version', 'v1.0');

            // Handle threshold scale
            $rawThreshold = AiSetting::get('ai_threshold', 70);
            $data['currentThreshold'] = (float) ($rawThreshold > 1 ? $rawThreshold / 100 : $rawThreshold * 100);


            // How many tickets did the AI route?
            $totalAiRouted = FeedbackPrediction::where('requires_intervention', false)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count();

            // How many were rejected by the departments due to AI errors?
            $totalRejected = TicketReturn::where('routing_source', 'AI Misclassification')
                ->whereBetween('returned_at', [$fromDate, $toDate])
                ->distinct('fbk_id')
                ->count('fbk_id');

            $successfulRoutes = max(0, $totalAiRouted - $totalRejected);

            $data['totalAiRouted'] = $totalAiRouted;
            $data['aiSuccessRate'] = $totalAiRouted > 0 
                ? round(($successfulRoutes / $totalAiRouted) * 100, 1) 
                : 0;

            // Total count of tickets rejected/dropped by departments due to AI (Filtered)
            $data['totalMisclassifications'] = TicketReturn::where('routing_source', 'AI Misclassification')
                ->whereBetween('returned_at', [$fromDate, $toDate])
                ->count();

            // Breakdown of misclassifications by Department
            $data['misclassificationsByDept'] = Ticket::join('ticket_returns', 'tickets.tck_id', '=', 'ticket_returns.tck_id')
                ->where('ticket_returns.routing_source', 'AI Misclassification')
                ->whereBetween('ticket_returns.returned_at', [$fromDate, $toDate])
                ->selectRaw('tickets.dep_id, COUNT(*) as count')
                ->with('department')
                ->groupBy('tickets.dep_id')
                ->orderByDesc('count')
                ->get();

            // Active Triage Table: Kicked back by departments waiting for Admin correction (Not filtered by date, as triage requires immediate attention regardless of date)
            $data['misclassifiedTickets'] = TicketReturn::join('tickets', 'ticket_returns.tck_id', '=', 'tickets.tck_id')
                ->join('feedbacks', 'ticket_returns.fbk_id', '=', 'feedbacks.fbk_id')
                ->leftJoin('departments', 'tickets.dep_id', '=', 'departments.dep_id')
                ->where('feedbacks.fbk_status', 0) 
                ->select(
                    'ticket_returns.*', 
                    'tickets.dep_id', 
                    'departments.dep_name', 
                    'feedbacks.fbk_details'
                )
                ->orderBy('ticket_returns.returned_at', 'desc')
                ->take(10)
                ->get();


            $data['aiApiStatus'] = Cache::remember('ml_api_status', 60, function () {
                try {
                    // Timeout prevents dashboard from hanging if python server is dead
                    $response = Http::timeout(3)->get(config('services.ml_api.url') . '/');
                    
                    if ($response->successful()) {
                        $health = $response->json();
                        // Optional: You can also check $health['model_loaded'] here if you want
                        if (isset($health['status']) && $health['status'] === 'online') {
                            return 'Online';
                        }
                    }
                    return 'Offline';
                } catch (\Exception $e) {
                    return 'Offline';
                }
            });

            // ==========================================
            // NEW: Confidence Bracket Metrics (For Report Table)
            // ==========================================
            
            $decimalThreshold = $rawThreshold > 1 ? $rawThreshold / 100 : (float)$rawThreshold;

            $data['countHighPrecision'] = FeedbackPrediction::whereHas('topCandidate', function ($query) {
                    $query->where('probability', '>=', 0.85);
                })
                ->where('requires_intervention', false)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count();

            $data['countStandardConfidence'] = FeedbackPrediction::whereHas('topCandidate', function ($query) use ($decimalThreshold) {
                    $query->where('probability', '>=', $decimalThreshold)
                          ->where('probability', '<', 0.85);
                })
                ->where('requires_intervention', false)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count();

            $data['countLowConfidence'] = FeedbackPrediction::whereHas('topCandidate', function ($query) use ($decimalThreshold) {
                    $query->where('probability', '<', $decimalThreshold);
                })
                ->where('requires_intervention', true)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count();

            $data['countRestrictedOverride'] = FeedbackPrediction::whereHas('topCandidate', function ($query) use ($decimalThreshold) {
                    $query->where('probability', '>=', $decimalThreshold);
                })
                ->where('requires_intervention', true)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->count();
        }


        // 3. Validator Data
        if ($user->hasAnyRole(['Validator'])) {
            
            $data['validationCount'] = Feedback::pending()->count(); 
            
            $data['validatedTodayCount'] = Feedback::validated()
                ->where('fbk_validated_by', $user->usr_id)
                ->whereDate('fbk_date_validated', today())
                ->count();

            // Urgent/Aging Feedbacks (e.g., older than 24 hours waiting for validation)
            $data['urgentCount'] = Feedback::pending()
                ->where('fbk_date_created', '<', now()->subHours(24))
                ->count();

            // Breakdown by Feedback Type (Complaint, Inquiry, Suggestion, etc.)
            $data['pendingByType'] = Feedback::pending()
                ->selectRaw('typ_id, count(*) as total')
                ->with('type') // Assuming the relationship is 'type' in your Feedback model
                ->groupBy('typ_id')
                ->get();

            $data['pendingByBranch'] = Feedback::pending()
                ->selectRaw('branch_id, count(*) as total')
                ->with('branch')
                ->groupBy('branch_id')
                ->get();
        }

        // 4. Department Head Data
        if ($user->hasAnyRole(['Department Head'])) {
            $deptIds = $user->departments()->pluck('departments.dep_id');
            
            // Total pending action across all their departments
            $data['actionCount'] = Ticket::whereIn('dep_id', $deptIds)
                ->active()
                ->pendingAction()
                ->count();

            // Urgent/SLA Risk: Tickets waiting for action for more than 24 hours
            $data['urgentActionCount'] = Ticket::whereIn('dep_id', $deptIds)
                ->active()
                ->pendingAction()
                ->where('tck_date_created', '<', now()->subHours(24))
                ->count();

            // Tickets they personally responded to today
            $data['actionedTodayCount'] = Ticket::where('tck_action_by', $user->usr_id)
                ->whereDate('tck_date_action', today())
                ->count();

            // Pass their departments (your model already counts pending tickets beautifully!)
            $data['myDepartments'] = $user->departments;

            // Recent tickets they provided action for
            $data['recentActions'] = Ticket::with('department')
                ->where('tck_action_by', $user->usr_id)
                ->orderBy('tck_date_action', 'desc')
                ->take(5)
                ->get();
        }

        // 5. Verifier Data
        if ($user->hasAnyRole(['Super Admin', 'Verifier'])) {
            
            // Define pending actions: Active, not yet verified, and not rejected
            $pendingActionsQuery = Action::active()
                ->where('act_status', 0)
                ->whereNull('act_date_verified')
                ->whereNull('act_reject_details')
                ->whereHas('ticket', function ($query) {
                    $query->where('tck_active', 1); // Only count if parent ticket is active
                });

            // Total pending verification
            $data['verificationCount'] = (clone $pendingActionsQuery)->count();

            // Urgent/Aging: Action created > 24 hours ago, still waiting for verification
            $data['urgentVerificationCount'] = (clone $pendingActionsQuery)
                ->where('act_date_created', '<', now()->subHours(24))
                ->count();

            // Productivity: How many actions they verified today
            $data['verifiedTodayCount'] = Action::active()
                ->verified()
                ->where('act_verified_by', $user->usr_id)
                ->whereDate('act_date_verified', today())
                ->count();

            // Pending Queue Grouped by Department
            // We join the tickets and departments tables to count actions per department
            $data['verificationByDept'] = (clone $pendingActionsQuery)
                ->join('tickets', 'actions.tck_id', '=', 'tickets.tck_id')
                ->join('departments', 'tickets.dep_id', '=', 'departments.dep_id')
                ->selectRaw('departments.dep_name, count(actions.act_id) as total')
                ->groupBy('departments.dep_name')
                ->get();

            // Their recent verification history
            $data['recentVerifications'] = Action::with('ticket.department')
                ->active()
                ->verified()
                ->where('act_verified_by', $user->usr_id)
                ->orderBy('act_date_verified', 'desc')
                ->take(5)
                ->get();
        }

        // 6. Reports Viewing Data
        if ($user->hasAnyRole(['Reports Viewing'])) {
            $currentMonth = now()->startOfMonth();

            // 1. Transaction Overview (Current Month)
            $data['totalFeedbackThisMonth'] = Feedback::where('fbk_date_created', '>=', $currentMonth)->count();
            $data['resolvedTicketsThisMonth'] = Ticket::whereNotNull('tck_rate')
                ->where('tck_date_created', '>=', $currentMonth)
                ->count();

            // 2. CSAT Overview (Mocking a rating column - adjust to your schema)
            // Assuming a 5-star rating system on the feedback or a related survey table
            $data['averageCsat'] = Ticket::whereNotNull('tck_rate')
                ->where('tck_date_created', '>=', $currentMonth)
                ->avg('tck_rate') ?? 0;

            // 3. Bottleneck Analysis (Tickets stuck the longest)
            $data['openTicketsCount'] = Ticket::active()->count();
            $data['overdueTicketsCount'] = Ticket::active()
                ->where('tck_date_created', '<', now()->subDays(3)) // e.g., 3-day SLA
                ->count();
        }

        return view('dashboard', $data);
    }

    public function settings()
    {
        return view('settings');
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'usr_name' => 'required|string|max:255',
            'usr_mobile' => 'nullable|digits:10',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|required_with:current_password|min:6|confirmed',
        ]);

        // Update Profile Info
        $user->usr_name = $request->usr_name;
        $user->usr_mobile = $request->usr_mobile;

        // Handle Password Change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->usr_password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
            }
            $user->usr_password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Account settings updated successfully.');
    }
}
