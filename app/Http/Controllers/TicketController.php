<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\ThematicValue;
use App\Models\Ticket;
use App\Models\Department;

class TicketController extends Controller
{
    public function forValidation(Request $request)
    {
        $query = Feedback::with(['type', 'theme'])
            ->where('fbk_status', 0) // 0 = Pending
            ->where('fbk_active', 1);

        $feedbacks = $this->applyFeedbackFilters($query, $request)
            ->orderBy('fbk_date_created', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => Feedback::where('fbk_status', 0)->count(),
            'total_today' => Feedback::whereDate('fbk_date_created', today())->count(),
        ];

        return view('feedbacks.for_validation', compact('feedbacks', 'stats'));
    }

    public function forVerification(Request $request)
    {
        $query = Ticket::with(['feedback', 'department', 'actionBy'])
            ->where('tck_route', 1)           // 1 = Pending Verification
            ->whereNotNull('tck_date_action') // Must have action
            ->whereNull('tck_date_verified')  // Not yet verified
            ->where('tck_active', 1);

        $tickets = $this->applyTicketFilters($query, $request)
            ->orderBy('tck_date_action', 'desc')
            ->paginate(20);

        return view('tickets.for_verification', compact('tickets'));
    }

public function showDepartmentTickets($dep_id)
    {
        $user = auth()->user();

        // 1. Security: Ensure user has access to this specific department
        if (!$user->hasAnyRole('Super Admin') && !$user->hasDepartment($dep_id)) {
            abort(403, 'Unauthorized access to this department.');
        }

        // 2. Fetch Department and Tickets with relationships
        $department = Department::findOrFail($dep_id);

        $tickets = Ticket::where('dep_id', $dep_id)
            ->active() // Using your model scope
            ->with(['feedback', 'createdBy', 'actionBy', 'verifiedBy'])
            ->orderBy('tck_date_created', 'desc')
            ->paginate(15);

        return view('departments.show', compact('department', 'tickets'));
    }
}
