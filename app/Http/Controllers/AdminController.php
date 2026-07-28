<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\Role;
use App\Models\Branch;
use App\Models\FeedbackType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Models\FeedbackPrediction;
use Carbon\Carbon;

class AdminController extends Controller
{
    
    public function manage_departments(Request $request)
    {
        $search = $request->query('search');

        $departments = Department::with(['users' => fn($q) => $q->where('usr_active', 1)])
            ->withCount('tickets')
            ->when($search, function($query, $search) {
                $query->where('dep_name', 'like', "%{$search}%")
                    ->orWhere('dep_full_name', 'like', "%{$search}%");
            })
            ->orderBy('dep_name', 'asc')
            ->paginate(10)
            ->withQueryString(); // Keeps search parameter in pagination links

        $allUsers = User::where('usr_active', 1)->get();

        return view('admin.manage-departments', compact('departments', 'allUsers'));
    }

    public function updateDepartment(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        $dept->update($request->validate([
            'dep_name' => 'required|string|max:50',
            'dep_full_name' => 'required|string|max:255',
            'dep_active' => 'required|boolean'
        ]));

        return back()->with('success', 'Department updated successfully!');
    }

    /**
     * Handle assigning a user to a department (Pivot Table: user_departments)
     */
    public function assignUser(Request $request)
    {
        $request->validate([
            'usr_id' => 'required|exists:users,usr_id',
            'dep_id' => 'required|exists:departments,dep_id',
        ]);

        $user = User::findOrFail($request->usr_id);
        
        // syncWithoutDetaching prevents duplicate entries in user_departments
        // It uses the usr_id and dep_id columns automatically
        $user->departments()->syncWithoutDetaching([$request->dep_id]);

        return back()->with('success', 'User access updated successfully!');
    }

    /**
     * Remove a user's access from a department
     */
    public function removeUser($dep_id, $usr_id)
    {
        $department = Department::findOrFail($dep_id);
        
        // Removes the record from the user_departments pivot table
        $department->users()->detach($usr_id);

        return back()->with('success', 'User access revoked.');
    }

    /**
     * Display a listing of users
     */
    public function manage_users(Request $request)
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');
        $branchFilter = $request->has('branch') ? $request->query('branch') : auth()->user()->branch_id;

        $branches = Branch::all();
        $departments = Department::all()->groupBy('branch_id');

        $users = User::with('roles', 'departments')
            // 1. Group the OR conditions so the Role Filter isn't bypassed
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('usr_name', 'like', "%{$search}%")
                    ->orWhere('usr_code', 'like', "%{$search}%");
                });
            })
            // 2. Specify the table name to fix the "Ambiguous" error
            ->when($roleFilter, function($query, $roleFilter) {
                $query->whereHas('roles', function($q) use ($roleFilter) {
                    $q->where('roles.rol_id', $roleFilter); // Table name added here
                });
            })
            ->when($branchFilter, function($query, $branchFilter) {
                $query->where('branch_id', $branchFilter);
            })
            ->orderBy('usr_name', 'asc')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::where('rol_active', 1)->get();

        return view('admin.manage-users', compact('users', 'roles', 'branches', 'departments', 'branchFilter'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'roles' => 'required|array',
            'departments' => 'required|array',
            'usr_active' => 'required|boolean'
        ]);

        // Only update the active status
        $user->update([
            'usr_active' => $request->usr_active
        ]);

        // Sync pivot table for roles
        $user->roles()->sync($request->roles); 
        $user->departments()->sync($request->departments);

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usr_name'  => 'required|string|max:255',
            'usr_code'  => 'required|unique:users,usr_code',
            'usr_email' => 'required|email|unique:users',
            'branch_id'   => 'required|string', 
            'roles'       => 'required|array',
            'departments' => 'required|array',
        ]);

        $user = User::create([
            'usr_name'      => $validated['usr_name'],
            'usr_code'      => $validated['usr_code'],
            'usr_email'     => $validated['usr_email'],
            'branch_id'     => $validated['branch_id'],
            'usr_active'    => 1,
        ]);


        if (!empty($validated['roles'])) {
            $user->roles()->attach($validated['roles']);
        }

        if (!empty($validated['departments'])) {
            $user->departments()->attach($validated['departments']);
        }

        try {
            $signedUrl = URL::temporarySignedRoute(
                'password.setup', 
                now()->addHours(24), 
                ['user' => $user->usr_id]
            );

            Mail::to($user->usr_email)->send(new \App\Mail\WelcomeUserMail($user, $signedUrl));

            return redirect()->back()->with('success', 'User created! An invitation email has been sent.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'User was created, but the invitation email failed to send. Please contact IT.');
        }


        return redirect()->back()->with('success', 'User created! An invitation email has been sent.');
    }

    public function resolvedTickets(Request $request)
    {
        // 1. Start query for RESOLVED tickets (assuming tck_date_action being filled means it's done)
        $query = Ticket::whereNotNull('tck_date_action')
            ->with(['feedback.type', 'feedback.theme', 'feedback.branch', 'department', 'actionBy']);

        // 2. Search Filter (Ticket ID, Student Name, or Action Details)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tck_id', 'like', "%{$search}%")
                  ->orWhere('tck_action_details', 'like', "%{$search}%")
                  ->orWhereHas('feedback', function($fbQuery) use ($search) {
                      $fbQuery->where('std_name', 'like', "%{$search}%")
                              ->orWhere('std_id_no', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Department Filter
        if ($request->filled('dep_id')) {
            $query->where('dep_id', $request->dep_id);
        }

        // 4. Branch Filter (Via Feedback)
        if ($request->filled('branch_id')) {
            $query->whereHas('feedback', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // 5. Date Range Filter (Using the date the ticket was resolved)
        if ($request->filled('date_from')) {
            $query->whereDate('tck_date_action', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tck_date_action', '<=', $request->date_to);
        }

        // 6. Sort by most recently resolved and Paginate
        $tickets = $query->orderBy('tck_date_action', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        // 7. Get filter dropdown data
        $departments = Department::all();
        $branches = Branch::all();
        $types = FeedbackType::all();

        return view('admin.resolved_tickets', compact('tickets', 'departments', 'branches', 'types'));
    }


    /**
     * Exports report in dashboard
     */
    public function exportReport(Request $request)
    {
        // 1. Validate incoming modal data
        $validated = $request->validate([
            'report_type' => 'required|string|in:intervention_summary,triage_audit,raw_predictions',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'intervention_filter' => 'required|string|in:all,auto_only,manual_only',
            'format' => 'required|string|in:pdf,csv',
        ]);

        // Format dates to cover the entire start/end days
        $fromDate = Carbon::parse($validated['date_from'])->startOfDay();
        $toDate = Carbon::parse($validated['date_to'])->endOfDay();

        // 2. Base Query for Predictions
        $query = FeedbackPrediction::with(['topCandidate.department', 'actionTakenBy'])
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($validated['intervention_filter'] === 'auto_only') {
            $query->where('requires_intervention', false);
        } elseif ($validated['intervention_filter'] === 'manual_only') {
            $query->where('requires_intervention', true);
        }

        // 3. Handle CSV Export
        if ($validated['format'] === 'csv') {
            $fileName = $validated['report_type'] . '_' . now()->format('Y_m_d_His') . '.csv';

            return Response::streamDownload(function () use ($query, $validated) {
                $file = fopen('php://output', 'w');

                if ($validated['report_type'] === 'raw_predictions') {
                    // CSV Headers for Data Science / Raw Export
                    fputcsv($file, [
                        'Prediction ID', 'Feedback ID', 'Word Count', 'Detected Language',
                        'Predicted Category ID', 'Category Confidence',
                        'Top Dept ID', 'Top Dept Name', 'Dept Probability', 
                        'Requires Intervention', 'Action Taken', 'Date Created'
                    ]);

                    // Chunking prevents memory crashes on large tables
                    $query->chunk(500, function ($predictions) use ($file) {
                        foreach ($predictions as $pred) {
                            fputcsv($file, [
                                $pred->id,
                                $pred->fbk_id,
                                $pred->input_word_count,
                                strtoupper($pred->detected_language ?? 'UNKNOWN'), 
                                $pred->predicted_category,
                                $pred->category_confidence,
                                $pred->topCandidate->dep_id ?? 'N/A',
                                $pred->topCandidate->department->dep_name ?? 'N/A',
                                $pred->topCandidate->probability ?? 'N/A',
                                $pred->requires_intervention ? 'Yes' : 'No',
                                $pred->action_taken ?? 'None',
                                $pred->created_at->format('Y-m-d H:i:s')
                            ]);
                        }
                    });

                } elseif ($validated['report_type'] === 'triage_audit') {
                    // CSV Headers for Audit Export
                    fputcsv($file, ['Feedback ID', 'Action Taken', 'Action By', 'Action Date']);
                    
                    $query->chunk(500, function ($predictions) use ($file) {
                        foreach ($predictions as $pred) {
                            fputcsv($file, [
                                $pred->fbk_id,
                                $pred->action_taken,
                                $pred->actionTakenBy->usr_name ?? 'System',
                                $pred->action_taken_at ? $pred->action_taken_at->format('Y-m-d H:i:s') : 'N/A'
                            ]);
                        }
                    });

                } elseif ($validated['report_type'] === 'intervention_summary') {
                    // NEW: CSV Headers for Intervention Summary
                    fputcsv($file, ['Metric', 'Count', 'Percentage']);

                    $total = (clone $query)->count();
                    $autoRouted = (clone $query)->where('requires_intervention', false)->count();
                    $humanTriaged = (clone $query)->where('requires_intervention', true)->count();

                    fputcsv($file, ['Total Predictions Evaluated', $total, '100%']);
                    fputcsv($file, ['Auto-Routed', $autoRouted, $total > 0 ? round(($autoRouted / $total) * 100, 1) . '%' : '0%']);
                    fputcsv($file, ['Human Interventions', $humanTriaged, $total > 0 ? round(($humanTriaged / $total) * 100, 1) . '%' : '0%']);

                    fputcsv($file, []); // Blank row for spacing
                    fputcsv($file, ['Source Language', 'Count', 'Percentage']);

                    $langStats = (clone $query)->select('detected_language', \DB::raw('COUNT(*) as count'))
                        ->whereNotNull('detected_language')
                        ->groupBy('detected_language')
                        ->orderByDesc('count')
                        ->get();
                        
                    $totalLang = $langStats->sum('count');
                    $languageMap = ['en' => 'English', 'tl' => 'Tagalog', 'ceb' => 'Cebuano', 'unknown' => 'Unknown'];

                    foreach ($langStats as $item) {
                        $rawCode = strtolower($item->detected_language);
                        $formatted_language = $languageMap[$rawCode] ?? strtoupper($rawCode);
                        $percentage = $totalLang > 0 ? round(($item->count / $totalLang) * 100, 1) . '%' : '0%';
                        
                        fputcsv($file, [$formatted_language, $item->count, $percentage]);
                    }
                }

                fclose($file);
            }, $fileName, [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        // 4. Handle PDF / Print View Export
        // Gather aggregates based on the date range for the visual report
        $reportData = [
            'date_from' => $fromDate->format('M d, Y'),
            'date_to' => $toDate->format('M d, Y'),
            'report_type' => $validated['report_type'],
            'total_predictions' => $query->count(),
        ];

        if ($validated['report_type'] === 'intervention_summary') {
            $reportData['auto_routed'] = (clone $query)->where('requires_intervention', false)->count();
            $reportData['human_triaged'] = (clone $query)->where('requires_intervention', true)->count();
        
            $langStats = (clone $query)->select('detected_language', \DB::raw('COUNT(*) as count'))
                ->whereNotNull('detected_language')
                ->groupBy('detected_language')
                ->orderByDesc('count')
                ->get();

            $languageMap = ['en' => 'English', 'tl' => 'Tagalog', 'ceb' => 'Cebuano', 'unknown' => 'Unknown'];
            $totalLanguagePredictions = $langStats->sum('count'); 
            
            $reportData['languages'] = $langStats->map(function ($item) use ($languageMap, $totalLanguagePredictions) {
                $rawCode = strtolower($item->detected_language);
                $item->formatted_language = $languageMap[$rawCode] ?? strtoupper($rawCode);
                
                $item->percentage = $totalLanguagePredictions > 0 
                    ? round(($item->count / $totalLanguagePredictions) * 100, 1) 
                    : 0;
                return $item;
            });

        } elseif ($validated['report_type'] === 'triage_audit') {
            $reportData['rejections'] = Ticket::with(['department', 'actionBy', 'feedback'])
                ->whereBetween('tck_date_action', [$fromDate, $toDate])
                ->where('tck_active', 0)
                ->where('tck_disapprove_details', 'LIKE', '%Misclassified by AI%')
                ->get();
        }

        // Render a clean Blade view meant for printing/PDF conversion
        return view('reports.export_layout', compact('reportData', 'validated'));
    }

    public function generateUniqueCode()
    {
        do {
            $code = mt_rand(1000, 9999);
            
        } while (User::where('usr_code', $code)->exists());

        return response()->json(['code' => $code]);
    }
}
