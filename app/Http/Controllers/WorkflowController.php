<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;
use App\Models\Ticket;
use App\Models\FeedbackType;
use App\Models\Branch;
use App\Models\ThematicValue;
use App\Models\Department;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class WorkflowController extends Controller
{
    // STAGE 1: ENCODE
    public function encodeIndex() 
    {
        $branches = Branch::all(); // Fetches all UM branches
        $types = FeedbackType::where('typ_active', 1)->get();
        
        // We pass all active themes; we will filter them in the browser using JS
        $themes = ThematicValue::where('thm_active', 1)->get();
        
        return view('workflow.encode', compact('branches', 'types', 'themes'));
    }

    public function storeManual(Request $request) 
    {
        $request->validate([
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'branch_id' => 'required',
            'typ_id' => 'required',
            'message' => 'required',
        ]);

        // Merge names for the 'std_name' column in your model
        $fullName = trim("{$request->last_name}, {$request->first_name} {$request->middle_initial}");

        Feedback::create([
            'std_id_no'   => $request->id_number,
            'std_name'    => $fullName,
            'std_email'   => $request->email,
            'std_mobile'  => $request->phone,
            'std_program' => $request->college_program,
            'branch_id'   => $request->branch_id,
            'typ_id'      => $request->typ_id,
            'thm_id'      => $request->thm_id, // Thematic Value
            'fbk_details' => $request->message,
            'fbk_status'  => 0, // Set to 'Pending' for the Validation stage
            'fbk_date_created' => now(),
            'fbk_created_by'   => auth()->id(),
        ]);

        return redirect()->route('workflow.encode')->with('success', 'Feedback encoded successfully.');
    }

    // STAGE 2: VALIDATION (Raw -> Ticket)
    public function validationIndex(Request $request) 
    {
        $query = Feedback::pending()->with([
            'type', 
            'theme', 
            'prediction.candidates.department' 
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('std_name', 'like', "%{$search}%")
                ->orWhere('std_id_no', 'like', "%{$search}%")
                ->orWhere('fbk_details', 'like', "%{$search}%");
            });
        }

        // 3. Branch Filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // 4. Feedback Type Filter
        if ($request->filled('typ_id')) {
            $query->where('typ_id', $request->typ_id);
        }

        // 5. Date Range Filters (Using the fbk_date_created column)
        if ($request->filled('date_from')) {
            $query->whereDate('fbk_date_created', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('fbk_date_created', '<=', $request->date_to);
        }

        // 6. Sort by Latest First and Paginate
        $feedbacks = $query->orderBy('fbk_date_created', 'desc')
                        ->paginate(10)
                        ->withQueryString(); // Keeps filter params in the URL when changing pages
        
        // 7. Fetch dropdown data
        // $departments = Department::where('dep_active', 1)->get();
        $departments = Department::with('branch') // Load branch data in one go
                        ->where('dep_active', 1)
                        ->get()
                        ->groupBy('branch_id'); // Or group by branch->branch_name

        $branches = Branch::all();
        $types = FeedbackType::where('typ_active', 1)->get();

        $threshold = \App\Models\AiSetting::get('prediction_threshold', 0.50);
        $aiEnabled = \App\Models\AiSetting::get('ai_enabled', 'yes') === 'yes';

        return view('workflow.validation', compact(
            'feedbacks', 'departments', 'branches', 'types', 
            'threshold', 'aiEnabled' 
        ));
    }

    public function processValidation(Request $request, $id) 
    {
        $fb = Feedback::findOrFail($id);

        if ($request->action == 'approve') {

            // Decode tagify
            $selectedData = json_decode($request->dep_ids, true); 

            if (empty($selectedData)) {
                return back()->with('error', 'Please select at least one department.');
            }

            DB::transaction(function () use ($fb, $selectedData) {
                
                $fb->update([
                    'fbk_status' => 1, 
                    'fbk_date_validated' => now(),
                    'fbk_validated_by' => Auth::id()
                ]);

                // 2. Create ONE ticket
                $ticket = Ticket::create([
                    'tck_uuid' => (string) Str::uuid(),
                    'fbk_id' => $fb->fbk_id,
                    'tck_date_created' => now(),
                    'tck_active' => 1
                ]);

                $depIds = collect($selectedData)->pluck('value')->toArray();
                $ticket->departments()->attach($depIds);
            });

            return redirect()->route('workflow.validation')->with('success', 'Feedback approved and tickets generated.');
        }

        if ($request->action == 'reject') {
            $fb->update([
                'fbk_status' => 2, // 2 = Dropped/Disapproved
                'fbk_disapprove_details' => $request->fbk_disapprove_details,
                'fbk_date_validated' => now(),
                'fbk_validated_by' => Auth::id()
            ]);

            return redirect()->route('workflow.validation')->with('info', 'Feedback has been dropped.');
        }

        return back();
    }

    public function validationDetails($id)
    {
        // Fetch the feedback and eager load necessary relationships to prevent N+1 queries
        $feedback = Feedback::with([
            'type', 
            'theme', 
            'prediction.candidates.department'
        ])->findOrFail($id);

        if ($feedback->fbk_status !== 0) 
        {
            $statusLabel = $feedback->fbk_status == 1 ? 'approved' : 'dropped';
            return redirect()->route('workflow.validation')
                ->with('warning', "This feedback (ID: {$id}) has already been {$statusLabel} and cannot be edited.");
        }

        // Fetch AI Settings
        $aiEnabled = DB::table('ai_settings')->where('key', 'ai_enabled')->value('value') === 'on';
        
        // Convert threshold to decimal (e.g., 70 becomes 0.70) if needed, depending on your DB
        $rawThreshold = DB::table('ai_settings')->where('key', 'ai_threshold')->value('value') ?? 70;
        $threshold = $rawThreshold > 1 ? $rawThreshold / 100 : $rawThreshold;

        // Fetch departments grouped by branch for the dropdown
        $departments = Department::orderBy('branch_id')
            ->orderBy('dep_name')
            ->get()
            ->groupBy('branch_id');
        
        $prediction = DB::table('feedback_predictions')
            ->where('fbk_id', $feedback->fbk_id)
            ->first();

        $candidates = [];
        if ($prediction) {
            $candidates = DB::table('prediction_candidates')
                ->join('departments', 'prediction_candidates.dep_id', '=', 'departments.dep_id')
                ->where('prediction_id', $prediction->id)
                ->orderBy('rank', 'asc')
                ->select('departments.dep_name', 'prediction_candidates.*')
                ->get();
        }

        return view('feedback_to_validate', compact(
            'feedback', 
            'aiEnabled', 
            'threshold', 
            'departments',
            'prediction',
            'candidates'
        ));
    }

    public function autocompleteDepartments(Request $request): JsonResponse
    {
        $query = $request->get('query');

        $data = Department::with('branch')
            ->where('dep_name', 'LIKE', "%{$query}%")
            ->take(10)
            ->get()
            ->map(function ($dep) {
                return [
                    'value' => $dep->dep_id,
                    'name'  => $dep->dep_name,
                    'branch' => $dep->branch->branch_name ?? "Branch {$dep->branch_id}"
                ];
            });

        return response()->json($data);
    }

    // STAGE 3: ACTION (Department Response)
    public function actionIndex(Request $request) 
    {
        $query = Ticket::active()->pendingAction()->with(['feedback.type', 'feedback.theme']);

        // Filter to only display User's department
        $userDepartmentIds = DB::table('user_departments')
            ->where('usr_id', auth()->id())
            ->pluck('dep_id')
            ->toArray();

        $query->whereIn('dep_id', $userDepartmentIds);

        // 2. Search Filter (Ticket ID, or Feedback details/student name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tck_id', 'like', "%{$search}%")
                ->orWhereHas('feedback', function($fbQuery) use ($search) {
                    $fbQuery->where('std_name', 'like', "%{$search}%")
                            ->orWhere('std_id_no', 'like', "%{$search}%")
                            ->orWhere('fbk_details', 'like', "%{$search}%");
                });
            });
        }

        // 3. Branch Filter (Via Feedback relationship)
        if ($request->filled('branch_id')) {
            $query->whereHas('feedback', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // 4. Type Filter (Via Feedback relationship)
        if ($request->filled('typ_id')) {
            $query->whereHas('feedback', function($q) use ($request) {
                $q->where('typ_id', $request->typ_id);
            });
        }

        // 5. Date Filter (Assuming tickets have a tck_date_created or created_at)
        if ($request->filled('date_from')) {
            $query->whereDate('tck_date_created', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tck_date_created', '<=', $request->date_to);
        }

        // 6. Sort and Paginate
        $tickets = $query->latest('tck_date_created')->paginate(10)->withQueryString();

        // 7. Data for dropdowns
        $branches = Branch::all();
        $types = FeedbackType::where('typ_active', 1)->get();

        return view('workflow.action', compact('tickets', 'branches', 'types'));
    }

    public function submitAction(Request $request, $id) 
    {
        $request->validate([
            'details' => 'required|string|min:5',
            'act_file' => 'nullable|file|mimes:pdf,jpg,png,docx|max:5120', // 5MB limit
        ]);

        $ticket = Ticket::findOrFail($id);

        // 1. Handle File Upload
        $filePath = null;
        if ($request->hasFile('act_file')) {
            // Stores in storage/app/public/actions_evidence
            $filePath = $request->file('act_file')->store('actions_evidence', 'public');
        }

        // 2. Create the entry in the actions table
        Action::create([
            'act_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'tck_id' => $ticket->tck_id,
            'act_details' => $request->details,
            'act_date_created' => now(),
            'act_created_by' => auth()->id(),
            'act_file' => $filePath,
            'act_status' => 0, // 0 = Pending Verification
            'act_active' => 1,
        ]);

        // 3. Update Ticket status to 'Pending Verification'
        $ticket->update([
            'tck_date_action' => now(),
            'tck_action_by' => auth()->id(),
        ]);

        return back()->with('success', 'Action recorded and submitted for verification.');
    }

    // STAGE 4: VERIFICATION (Final Approval/Rating)
    public function verificationIndex() 
    {
        // Eager load feedback and the latest actions (including the staff who wrote them)
        $tickets = Ticket::pendingVerification()
            ->with(['feedback', 'actions' => function($query) {
                $query->latest('act_date_created');
            }, 'actions.creator'])
            ->get();

        return view('workflow.verification', compact('tickets'));
    }

    // Stage 4: Verify & Rate
    public function verifyFinal(Request $request, $id) 
    {
        $ticket = Ticket::findOrFail($id);
        
        // Get the latest pending action
        $latestAction = $ticket->actions()->where('act_status', 0)->latest('act_date_created')->first();

        if($request->status == 'accept') {
            // Approve the specific action
            $latestAction?->update([
                'act_status' => 1,
                'act_date_verified' => now(),
                'act_verified_by' => auth()->id()
            ]);

            // Close the ticket
            $ticket->update([
                'tck_date_verified' => now(),
                'tck_verified_by' => auth()->id(),
                'tck_rate' => $request->tck_rate,
                'tck_rate_date' => now(),
            ]);
            return back()->with('success', 'Feedback closed and rated.');
        } 
        
        // ON REJECTION:
        $latestAction?->update([
            'act_status' => 2, // 2 = Rejected/Disapproved
            'act_reject_details' => $request->remarks,
        ]);

        // Send ticket back to the Department's "Pending" list
        $ticket->update(['tck_date_action' => null]); 

        return back()->with('info', 'Action rejected and sent back to department.');
    }

    public function departmentActionIndex(Request $request, $dep_id) 
    {
        // 1. Fetch the specific department (to optionally display its name in the view)
        $currentDepartment = Department::findOrFail($dep_id);

        // 2. Query pending tickets strictly for THIS department
        $query = Ticket::active()
            ->pendingAction()
            ->where('dep_id', $dep_id)
            ->with(['feedback.type', 'feedback.theme']);

        // 3. Search Filter (Ticket ID, or Feedback details/student name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tck_id', 'like', "%{$search}%")
                ->orWhereHas('feedback', function($fbQuery) use ($search) {
                    $fbQuery->where('std_name', 'like', "%{$search}%")
                            ->orWhere('std_id_no', 'like', "%{$search}%")
                            ->orWhere('fbk_details', 'like', "%{$search}%");
                });
            });
        }

        // 4. Branch Filter (Via Feedback relationship)
        if ($request->filled('branch_id')) {
            $query->whereHas('feedback', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // 5. Type Filter (Via Feedback relationship)
        if ($request->filled('typ_id')) {
            $query->whereHas('feedback', function($q) use ($request) {
                $q->where('typ_id', $request->typ_id);
            });
        }

        // 6. Date Filter
        if ($request->filled('date_from')) {
            $query->whereDate('tck_date_created', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tck_date_created', '<=', $request->date_to);
        }

        // 7. Sort and Paginate
        $tickets = $query->latest('tck_date_created')->paginate(10)->withQueryString();

        // 8. Data for dropdowns
        $branches = Branch::all();
        $types = FeedbackType::where('typ_active', 1)->get();

        // Pass everything to the exact same 'workflow.action' view
        return view('workflow.action', compact('tickets', 'branches', 'types', 'currentDepartment'));
    }

    public function showTicket($uuid) 
    {
        // Fetch ticket by UUID with all its history and related data
        $ticket = Ticket::where('tck_uuid', $uuid)
            ->with([
                'feedback', 
                'actions.creator', // Who did the action
                'actions.verifier', // Who approved/rejected it
                'department',
                'createdBy'
            ])
            ->firstOrFail();

        // Sort actions manually if not sorted by relationship
        $auditTrail = $ticket->actions->sortByDesc('act_date_created');

        return view('workflow.show_ticket', compact('ticket', 'auditTrail'));
    }
}