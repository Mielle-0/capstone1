<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Feedback;
use App\Models\FeedbackPrediction;
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
        $branches = Branch::all(); 
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
        $userBranchId = auth()->user()->branch_id ?? null;

        $query = Feedback::pending()
            ->where('branch_id', $userBranchId)
            ->with([
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
        $departments = Department::with('branch') 
                        ->where('dep_active', 1)
                        ->whereIn('branch_id', [$userBranchId, 'UM-MAIN'])
                        ->get()
                        ->groupBy('branch_id'); // Or group by branch->branch_name

        $types = FeedbackType::where('typ_active', 1)->get();

        $threshold = \App\Models\AiSetting::get('prediction_threshold', 0.50);
        $aiEnabled = \App\Models\AiSetting::get('ai_enabled', 'yes') === 'yes';

        return view('workflow.validation', compact(
            'feedbacks', 'departments', 'types', 
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

                // Extract the department ID from the Tagify array
                // $departmentId = $selectedData[0]['value'];
                $departmentIds = collect($selectedData)->pluck('value')->toArray();

                // 2. Update the Prediction Ground Truth!
                // This is how you capture the human intervention for your report.
                FeedbackPrediction::where('fbk_id', $fb->fbk_id)->update([
                    'verified_dept_ids' => $departmentIds,
                    'action_taken'      => 'manual_routing',
                    'action_taken_by'   => Auth::id(),
                    'action_taken_at'   => now(),
                ]);

                // 3. Loop through and create a ticket for EVERY selected department
                foreach ($departmentIds as $depId) {
                    Ticket::create([
                        'tck_uuid'         => (string) Str::uuid(),
                        'fbk_id'           => $fb->fbk_id,
                        'dep_id'           => $depId,
                        'tck_date_created' => now(),
                        'tck_active'       => 1
                    ]);
                }
            });

            return redirect()->route('workflow.validation')->with('success', 'Feedback approved and tickets generated.');
        }

        if ($request->action == 'reject') {
            DB::transaction(function () use ($fb, $request) {
                $fb->update([
                    'fbk_status' => 2, // 2 = Dropped/Disapproved
                    'fbk_disapprove_details' => $request->fbk_disapprove_details,
                    'fbk_date_validated' => now(),
                    'fbk_validated_by' => Auth::id()
                ]);

                // Optional: Flag the prediction so you know this feedback was entirely rejected
                FeedbackPrediction::where('fbk_id', $fb->fbk_id)->update([
                    'action_taken' => 'rejected_as_spam',
                    'action_taken_by' => Auth::id(),
                    'action_taken_at' => now(),
                ]);
            });

            return redirect()->route('workflow.validation')->with('info', 'Feedback has been dropped.');
        }

        return back();
    }

    public function validationDetails($id)
    {
        $userBranchId = auth()->user()->branch_id ?? null; 
        $mainBranchId = 'UM-MAIN';
        
        // OPTIMIZATION 1: Eager loading is already perfect here.
        $feedback = Feedback::with([
                'type', 
                'theme', 
                'prediction.candidates.department'
            ])
        ->find($id);

        // 1. Validation & Authorization
        if (!$feedback) {
            return redirect()->route('workflow.validation')
                ->with('error', "The feedback entry (ID: {$id}) could not be found or no longer exists.");
        }

        if ($feedback->branch_id !== $userBranchId) {
            return redirect()->route('workflow.validation')
                ->with('error', 'Unauthorized access. You can only view feedback assigned to your branch.');
        }

        if ($feedback->fbk_status !== 0) {
            $statusLabel = $feedback->fbk_status == 1 ? 'approved' : 'dropped';
            return redirect()->route('workflow.validation')
                ->with('warning', "This feedback (ID: {$id}) has already been {$statusLabel} and cannot be edited.");
        }

        // 2. Fetch Base Settings & Reference Data
        $aiEnabled = DB::table('ai_settings')->where('key', 'ai_enabled')->value('value') === 'on';
        $rawThreshold = DB::table('ai_settings')->where('key', 'ai_threshold')->value('value') ?? 70;
        $threshold = $rawThreshold > 1 ? $rawThreshold / 100 : $rawThreshold;
        $categories = FeedbackType::all(); 

        $allowedDepartments = Department::with('branch')
            ->where('dep_active', 1) 
            ->where(function($q) use ($userBranchId, $mainBranchId) {
                $q->where('branch_id', $mainBranchId);
                if ($userBranchId && $userBranchId !== $mainBranchId) {
                    $q->orWhere('branch_id', $userBranchId);
                }
            })
            ->orderBy('branch_id')
            ->orderBy('dep_name')
            ->get()
            ->map(function ($dep) {
                return [
                    'value' => $dep->dep_id,
                    'name'  => $dep->dep_name,
                    'branch' => $dep->branch_id 
                ];
            });
        
        // OPTIMIZATION 2: Use the eager-loaded relationship instead of Raw DB Queries
        $prediction = $feedback->prediction;
        $candidates = $prediction ? $prediction->candidates : collect();

        if ($candidates->isEmpty()) {
            
            session()->now('warning', 'AI prediction is currently unavailable or still processing. Please assign the category and department manually.');
        }

        // OPTIMIZATION 4: Pull category data directly from the DB. 
        // No HTTP API call needed here anymore!
        $predictedCategoryId = $prediction ? $prediction->predicted_category : null;
        $predictionConfidence = $prediction ? $prediction->category_confidence : null;

        return view('feedback_to_validate', compact(
            'feedback', 
            'aiEnabled', 
            'threshold', 
            'allowedDepartments',
            'prediction',
            'candidates',
            'categories',
            'predictedCategoryId',
            'predictionConfidence'
        ));
    }

    public function autocompleteDepartments(Request $request): JsonResponse
    {
        $query = $request->get('query');

        $userBranchId = auth()->user()->branch_id ?? null; 
        $mainBranchId = "UM-MAIN";

        $data = Department::with('branch')
            ->where(function ($q) use ($query) {
                $q->where('dep_name', 'LIKE', "{$query}%"); 
            })
            ->where(function ($q) use ($userBranchId, $mainBranchId) {
                if ($userBranchId) {
                    $q->where('branch_id', $userBranchId)
                      ->orWhere('branch_id', $mainBranchId);
                }
            })
            // Optional: Prioritize exact matches or alphabetical order
            ->orderBy('dep_name', 'asc') 
            ->take(10)
            ->get()
            ->map(function ($dep) {
                return [
                    'value' => $dep->dep_id,
                    'name'  => $dep->dep_name,
                    'branch' => $dep->branch->branch_name
                ];
            });

        return response()->json($data);
    }

    // STAGE 3: ACTION (Department Response)
    public function actionIndex(Request $request, $dep_id = null) 
    {
        // 1. Initialize base query
        $query = Ticket::active()
            ->pendingAction()
            ->with(['feedback.type', 'feedback.theme']);

        $currentDepartment = null;

        // 2. Department Filtering Logic
        if ($dep_id) {
            // Specific department logic (formerly departmentActionIndex)
            $currentDepartment = Department::findOrFail($dep_id);
            $query->where('dep_id', $dep_id);
        } else {
            // General logic (formerly actionIndex)
            $userDepartmentIds = DB::table('user_departments')
                ->where('usr_id', auth()->id())
                ->pluck('dep_id')
                ->toArray();
            
            $query->whereIn('dep_id', $userDepartmentIds);
        }

        // 3. Search Filter
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

        // 4. Branch Filter
        if ($request->filled('branch_id')) {
            $query->whereHas('feedback', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // 5. Type Filter
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

        $types = FeedbackType::where('typ_active', 1)->get();

        // Pass variables to view. $currentDepartment will naturally be null if no $dep_id was passed.
        return view('workflow.action', compact('tickets', 'types', 'currentDepartment'));
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

    public function dropTicket(Request $request, Ticket $ticket)
    {
        DB::transaction(function () use ($ticket, $request) {
            
            // 1. Drop the ticket
            $ticket->update([
                'tck_active' => 0,
                'tck_disapprove_details' => $request->input('reason', 'Misclassified by AI'),
                'tck_date_action' => now(),
                'tck_action_by' => auth()->id(),
            ]);

            // 2. Check if the parent Feedback has any other active tickets
            $activeTicketsCount = Ticket::where('fbk_id', $ticket->fbk_id)
                                        ->where('tck_active', 1)
                                        ->count();

            // 3. If no active tickets remain, revert the Feedback to pending (0)
            if ($activeTicketsCount === 0) {
                Feedback::where('fbk_id', $ticket->fbk_id)->update(['fbk_status' => 0]);
            }

            // 4. Update the Prediction so the AI report captures the failure
            FeedbackPrediction::where('fbk_id', $ticket->fbk_id)->update([
                'action_taken' => 'rejected_by_dept',
                // We clear the verified IDs because the AI was wrong, and an admin needs to set the right one later
                'verified_dept_ids' => null 
            ]);
            
        });

        return redirect()->back()->with('success', 'Ticket dropped and sent back to triage.');
    }

    public function showTimeline($id)
    {
        $userDepartmentIds = DB::table('user_departments')
            ->where('usr_id', auth()->id())
            ->pluck('dep_id')
            ->toArray();

        $feedback = Feedback::whereHas('tickets', function($query) use ($userDepartmentIds) {
            // This ensures the feedback has at least one ticket belonging to the user's department.
            // If they try to access a feedback ID not assigned to them, it throws a 404.
            $query->whereIn('dep_id', $userDepartmentIds)
                    ->where('tck_active', 1);
        })
        ->with([
            'validator', 
            // 3. Constrain the eager load so they only see their department's tickets in the timeline
            'tickets' => function($query) use ($userDepartmentIds) {
                $query->whereIn('dep_id', $userDepartmentIds)
                      ->where('tck_active', 1)
                      ->with([
                          'department', 
                          'actions.creator', 
                          'actions.verifier', 
                          'responses'
                      ]);
            }
        ])->findOrFail($id);

        return view('workflow.timeline', compact('feedback'));
    }

    // STAGE 4: VERIFICATION (Final Approval/Rating)
    public function verificationIndex() 
    {
        $actions = Action::active()
            ->where('act_status', 0)
            ->whereNull('act_date_verified')
            ->whereNull('act_reject_details')
            ->whereHas('ticket', function ($query) {
                $query->where('tck_active', 1); // Only count if parent ticket is active
            })
            ->with(['ticket.department', 'ticket.feedback', 'creator'])
            ->latest('act_date_created')
            ->get();

        return view('workflow.verification', compact('actions'));
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
                'tck_verified_by' => auth()->id()
            ]);
            return back()->with('success', 'Feedback closed and rated.');
        } 
        
        // ON REJECTION:
        $latestAction?->update([
            'act_status' => 2, // 2 = Rejected/Disapproved
            'act_reject_details' => $request->remarks,
            'act_date_verified' => now(),
            'act_verified_by' => auth()->id()
        ]);

        // Send ticket back to the Department's "Pending" list
        $ticket->update(['tck_date_action' => null]); 

        return back()->with('info', 'Action rejected and sent back to department.');
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