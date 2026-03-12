@extends('layouts.app')
@section('title', 'View Tables')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Feedbacks & Tickets Management</h2>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('feedbacks-tickets') }}" id="filterForm">
                <div class="row g-3">
                    <!-- View Type -->
                    <div class="col-md-3">
                        <label class="form-label">View</label>
                        <select name="view_type" class="form-select" id="viewType">
                            <option value="feedbacks" {{ request('view_type', 'feedbacks') == 'feedbacks' ? 'selected' : '' }}>Feedbacks</option>
                            <option value="tickets" {{ request('view_type') == 'tickets' ? 'selected' : '' }}>Tickets</option>
                        </select>
                    </div>

                    <!-- Feedback Filters -->
                    <div class="col-md-3 feedback-filter">
                        <label class="form-label">Feedback Status</label>
                        <select name="feedback_status" class="form-select">
                            <option value="">All Feedbacks</option>
                            <option value="with_ticket" {{ request('feedback_status') == 'with_ticket' ? 'selected' : '' }}>With Ticket</option>
                            <option value="without_ticket" {{ request('feedback_status') == 'without_ticket' ? 'selected' : '' }}>Without Ticket</option>
                            <option value="validated" {{ request('feedback_status') == 'validated' ? 'selected' : '' }}>Validated</option>
                            <option value="not_validated" {{ request('feedback_status') == 'not_validated' ? 'selected' : '' }}>Not Validated</option>
                        </select>
                    </div>

                    <!-- Ticket Filters -->
                    <div class="col-md-3 ticket-filter" style="display: none;">
                        <label class="form-label">Ticket Status</label>
                        <select name="ticket_status" class="form-select">
                            <option value="">All Tickets</option>
                            <option value="approved" {{ request('ticket_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="disapproved" {{ request('ticket_status') == 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                            <option value="pending_action" {{ request('ticket_status') == 'pending_action' ? 'selected' : '' }}>Pending Action</option>
                            <option value="pending_verification" {{ request('ticket_status') == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="active" {{ request('ticket_status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('ticket_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <!-- Branch Filter -->
                    <div class="col-md-3 feedback-filter">
                        <label class="form-label">Branch</label>
                        <select name="branch" class="form-select">
                            <option value="">All Branches</option>
                            <option value="UM-BANSALAN" {{ request('branch') == 'UM-BANSALAN' ? 'selected' : '' }}>UM-BANSALAN</option>
                            <option value="UM-DIGOS" {{ request('branch') == 'UM-DIGOS' ? 'selected' : '' }}>UM-DIGOS</option>
                            <option value="UM-MAIN" {{ request('branch') == 'UM-MAIN' ? 'selected' : '' }}>UM-MAIN</option>
                            <option value="UM-PANABO" {{ request('branch') == 'UM-PANABO' ? 'selected' : '' }}>UM-PANABO</option>
                            <option value="UM-TAGUM" {{ request('branch') == 'UM-TAGUM' ? 'selected' : '' }}>UM-TAGUM</option>
                        </select>
                    </div>

                    <!-- Department Filter (for tickets) -->
                    <div class="col-md-3 ticket-filter" style="display: none;">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept->dep_id }}" {{ request('department') == $dept->dep_id ? 'selected' : '' }}>
                                    {{ $dept->dep_name ?? 'Department ' . $dept->dep_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('feedbacks-tickets') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Feedbacks</h6>
                    <h3 class="mb-0">{{ $stats['total_feedbacks'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">With Tickets</h6>
                    <h3 class="mb-0">{{ $stats['feedbacks_with_tickets'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Without Tickets</h6>
                    <h3 class="mb-0">{{ $stats['feedbacks_without_tickets'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Tickets</h6>
                    <h3 class="mb-0">{{ $stats['total_tickets'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedbacks Table -->
    @if(request('view_type', 'feedbacks') == 'feedbacks')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Feedbacks</h5>
            <span class="badge bg-primary">{{ $feedbacks->total() }} results</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>UUID</th>
                            <th>Student</th>
                            <th>Branch</th>
                            <th>Type</th>
                            <th>Theme</th>
                            <th>Details</th>
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>Has Ticket</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $feedback)
                        <tr>
                            <td>{{ $feedback->fbk_id }}</td>
                            <td>
                                <small class="text-muted">{{ substr($feedback->fbk_uuid, 0, 8) }}...</small>
                            </td>
                            <td>
                                <strong>{{ $feedback->std_name }}</strong><br>
                                <small class="text-muted">{{ $feedback->std_id_no }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $feedback->branch_id }}</span>
                            </td>
                            <td>
                                @if($feedback->type)
                                    <span class="badge bg-info">{{ $feedback->type->typ_name ?? 'N/A' }}</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($feedback->theme)
                                    <span class="badge bg-light text-dark">{{ $feedback->theme->thm_name ?? 'N/A' }}</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $feedback->fbk_details }}
                                </div>
                            </td>
                            <td>
                                <small>{{ $feedback->fbk_date_created ? $feedback->fbk_date_created->format('M d, Y H:i') : 'N/A' }}</small>
                            </td>
                            <td>
                                @if($feedback->fbk_status == 1)
                                    <span class="badge bg-success">Validated</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($feedback->tickets_count > 0)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> {{ $feedback->tickets_count }}
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#feedbackModal{{ $feedback->fbk_id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Feedback Modal -->
                        <div class="modal fade" id="feedbackModal{{ $feedback->fbk_id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Feedback Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>UUID:</strong> {{ $feedback->fbk_uuid }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Branch:</strong> {{ $feedback->branch_id }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Student Name:</strong> {{ $feedback->std_name }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Student ID:</strong> {{ $feedback->std_id_no }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Email:</strong> {{ $feedback->std_email ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Mobile:</strong> {{ $feedback->std_mobile ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>Program:</strong> {{ $feedback->std_program }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>Details:</strong>
                                                <p class="mt-2 p-3 bg-light rounded">{{ $feedback->fbk_details }}</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Date Created:</strong> {{ $feedback->fbk_date_created ? $feedback->fbk_date_created->format('M d, Y H:i:s') : 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Date Validated:</strong> {{ $feedback->fbk_date_validated ? $feedback->fbk_date_validated->format('M d, Y H:i:s') : 'N/A' }}
                                            </div>
                                        </div>
                                        @if($feedback->tickets_count > 0)
                                        <div class="row">
                                            <div class="col-12">
                                                <strong>Associated Tickets:</strong>
                                                <span class="badge bg-info">{{ $feedback->tickets_count }} ticket(s)</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No feedbacks found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $feedbacks->firstItem() ?? 0 }} to {{ $feedbacks->lastItem() ?? 0 }} of {{ $feedbacks->total() }} entries
                </div>
                <div>
                    {{ $feedbacks->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tickets Table -->
    @if(request('view_type') == 'tickets')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tickets</h5>
            <span class="badge bg-primary">{{ $tickets->total() }} results</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Ticket ID</th>
                            <th>UUID</th>
                            <th>Feedback</th>
                            <th>Department</th>
                            <th>Date Created</th>
                            <th>Created By</th>
                            <th>Action Date</th>
                            <th>Verified Date</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td><strong>{{ $ticket->tck_id }}</strong></td>
                            <td>
                                <small class="text-muted">{{ substr($ticket->tck_uuid, 0, 8) }}...</small>
                            </td>
                            <td>
                                @if($ticket->feedback)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ticketFeedbackModal{{ $ticket->tck_id }}">
                                        <span class="badge bg-primary">Feedback #{{ $ticket->fbk_id }}</span>
                                    </a>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->department)
                                    <span class="badge bg-info">{{ $ticket->department->dep_name ?? 'Dept ' . $ticket->dep_id }}</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $ticket->tck_date_created ? $ticket->tck_date_created->format('M d, Y H:i') : 'N/A' }}</small>
                            </td>
                            <td>
                                @if($ticket->createdBy)
                                    <small>{{ $ticket->createdBy->name ?? 'User ' . $ticket->tck_created_by }}</small>
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                @if($ticket->tck_date_action)
                                    <small class="text-success">{{ $ticket->tck_date_action->format('M d, Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->tck_date_verified)
                                    <small class="text-success">{{ $ticket->tck_date_verified->format('M d, Y H:i') }}</small>
                                @else
                                    @if($ticket->tck_date_action)
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($ticket->tck_disapprove_details)
                                    <span class="badge bg-danger">Disapproved</span>
                                @elseif($ticket->tck_date_verified)
                                    <span class="badge bg-success">Approved</span>
                                @elseif($ticket->tck_date_action)
                                    <span class="badge bg-info">Pending Verification</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending Action</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($ticket->tck_active == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ticketModal{{ $ticket->tck_id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Ticket Modal -->
                        <div class="modal fade" id="ticketModal{{ $ticket->tck_id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Ticket Details #{{ $ticket->tck_id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Ticket UUID:</strong> {{ $ticket->tck_uuid }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Feedback ID:</strong> 
                                                @if($ticket->feedback)
                                                    <a href="#" class="badge bg-primary">{{ $ticket->fbk_id }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Department:</strong> 
                                                {{ $ticket->department->dep_name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Status:</strong>
                                                @if($ticket->tck_disapprove_details)
                                                    <span class="badge bg-danger">Disapproved</span>
                                                @elseif($ticket->tck_date_verified)
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($ticket->tck_date_action)
                                                    <span class="badge bg-info">Pending Verification</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending Action</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Created:</strong> {{ $ticket->tck_date_created ? $ticket->tck_date_created->format('M d, Y H:i:s') : 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Created By:</strong> {{ $ticket->createdBy->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Action Date:</strong> {{ $ticket->tck_date_action ? $ticket->tck_date_action->format('M d, Y H:i:s') : 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Action By:</strong> {{ $ticket->actionBy->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Verified Date:</strong> {{ $ticket->tck_date_verified ? $ticket->tck_date_verified->format('M d, Y H:i:s') : 'N/A' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Verified By:</strong> {{ $ticket->verifiedBy->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        @if($ticket->tck_disapprove_details)
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>Disapproval Details:</strong>
                                                <p class="mt-2 p-3 bg-danger bg-opacity-10 rounded text-danger">
                                                    {{ $ticket->tck_disapprove_details }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        @if($ticket->feedback)
                                        <div class="row">
                                            <div class="col-12">
                                                <strong>Related Feedback:</strong>
                                                <div class="mt-2 p-3 bg-light rounded">
                                                    <p><strong>Student:</strong> {{ $ticket->feedback->std_name }}</p>
                                                    <p><strong>Details:</strong> {{ $ticket->feedback->fbk_details }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Feedback Modal (for quick view from ticket table) -->
                        @if($ticket->feedback)
                        <div class="modal fade" id="ticketFeedbackModal{{ $ticket->tck_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Feedback #{{ $ticket->fbk_id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Student:</strong> {{ $ticket->feedback->std_name }}</p>
                                        <p><strong>Details:</strong></p>
                                        <p class="p-3 bg-light rounded">{{ $ticket->feedback->fbk_details }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tickets found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} entries
                </div>
                <div>
                    {{ $tickets->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge {
    font-weight: 500;
}

.table > tbody > tr:hover {
    background-color: rgba(0, 0, 0, 0.025);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewType = document.getElementById('viewType');
    const feedbackFilters = document.querySelectorAll('.feedback-filter');
    const ticketFilters = document.querySelectorAll('.ticket-filter');
    
    function toggleFilters() {
        const selectedView = viewType.value;
        
        if (selectedView === 'feedbacks') {
            feedbackFilters.forEach(el => el.style.display = 'block');
            ticketFilters.forEach(el => el.style.display = 'none');
        } else {
            feedbackFilters.forEach(el => el.style.display = 'none');
            ticketFilters.forEach(el => el.style.display = 'block');
        }
    }
    
    // Initial toggle
    toggleFilters();
    
    // Listen for changes
    viewType.addEventListener('change', toggleFilters);
});
</script>
@endsection