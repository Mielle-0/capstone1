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
use ArielMejiaDev\LarapexCharts\LarapexChart;
use App\Charts\VolumeChart;
use App\Charts\ClassificationChart;
use App\Charts\DepartmentChart;
use App\Charts\ConfidenceChart;


class AdminController extends Controller
{

    public function analytics()
    {
        // Load JSON data
        $json = file_get_contents('sample_feedback.json');
        $feedbacks = json_decode($json, true);

        // Stats calculation
        $total = count($feedbacks);
        $reviewed = count(array_filter($feedbacks, fn($f) => !empty($f['status'])));
        $routed = count(array_filter($feedbacks, fn($f) => !empty($f['department'])));
        $avg_confidence = array_sum(array_column($feedbacks, 'confidence')) / max($total, 1);

        $stats = [
            'total' => $total,
            'reviewed' => $reviewed,
            'routed' => $routed,
            'avg_confidence' => $avg_confidence,
        ];

        // Sample chart data from JSON
        $volumeChart = (new LarapexChart)->lineChart()
            ->addData('Feedback Count', [3, 5, 2, 4, 6, 3, 5])
            ->setHeight(250)
            ->setXAxis(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

        $classificationChart = (new LarapexChart)->pieChart()
            ->addData([
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Complaint')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Suggestion')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Commendation')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Inquiry')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Concern')),
            ])
            ->setHeight(250)
            ->setLabels(['Complaint', 'Suggestion', 'Commendation', 'Inquiry', 'Concern']);

        $departmentChart = (new LarapexChart)->barChart()
            ->addData('Feedbacks', [
                count(array_filter($feedbacks, fn($f) => $f['department'] === 'Registrar')),
                count(array_filter($feedbacks, fn($f) => $f['department'] === 'Library')),
                count(array_filter($feedbacks, fn($f) => $f['department'] === 'Facilities')),
            ])
            ->setHeight(250)
            ->setXAxis(['Registrar', 'Library', 'Facilities']);

        $confidenceChart = (new LarapexChart)->areaChart()
            ->addData('Low Confidence Count', [1, 0, 1, 0, 1]) // Just sample values
            ->setHeight(250)
            ->setXAxis(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);

        return view('admin.analytics', compact(
            'stats',
            'volumeChart',
            'classificationChart',
            'departmentChart',
            'confidenceChart'
        ));
    }
    
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

        $users = User::with('roles')
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
            ->orderBy('usr_name', 'asc')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::where('rol_active', 1)->get();

        return view('admin.manage-users', compact('users', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'usr_name' => 'required|string|max:255',
            'usr_code' => 'required|unique:users,usr_code,'.$id.',usr_id',
            'roles' => 'required|array'
        ]);

        $user->update($data);
        $user->roles()->sync($request->roles); // Sync pivot table

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'usr_code' => 'required|unique:users,usr_code',
            'usr_name' => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
            'roles'    => 'required|array'
        ]);

        $user = User::create([
            'usr_code'     => $request->usr_code,
            'usr_name'     => $request->usr_name,
            'usr_password' => Hash::make($request->password),
            'usr_active'   => 1,
        ]);

        // Attach selected roles in the user_roles pivot table
        $user->roles()->attach($request->roles);

        return back()->with('success', 'User created successfully!');
    }

    /**
     * Toggle user status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->usr_active = !$user->usr_active;
        $user->save();

        return back()->with('success', 'User status updated!');
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
}
