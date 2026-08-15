@extends('layouts.app')

@section('title', 'Action Verification')
@section('content')
<div class="container py-4">

    <!-- Changed from $tickets as $t to $actions as $action -->
    @forelse($actions as $action)
        
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="badge bg-maroon">Ticket #{{ $action->tck_id }}</span>
                <small class="text-muted">Assigned Dept: <strong>{{ $action->ticket->department->dep_name ?? 'N/A' }}</strong></small>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Left Side: Original Feedback (Accessed via $action->ticket) -->
                    <div class="col-md-5 border-end">
                        <h6 class="text-uppercase small fw-bold text-muted">Original Feedback</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <strong>{{ $action->ticket->feedback->std_name }}</strong> 
                            <small>({{ $action->ticket->feedback->std_program }})</small>
                            <p class="mt-2 mb-0 font-italic">"{{ $action->ticket->feedback->fbk_details }}"</p>
                        </div>
                        <small class="text-muted">Submitted: {{ $action->ticket->feedback->fbk_date_created->format('M d, Y h:i A') }}</small>
                    </div>

                    <!-- Right Side: The Action Resolution (Accessed directly via $action) -->
                    <div class="col-md-7 ps-md-4">
                        <h6 class="text-uppercase small fw-bold text-success">Department Resolution</h6>
                        
                        <div class="p-3 border border-success rounded bg-white shadow-sm mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold text-primary">{{ $action->creator->usr_name ?? 'Staff' }}</span>
                                <small class="text-muted">{{ $action->act_date_created->format('M d, Y h:i A') }}</small>
                            </div>
                            <p class="mb-2">{{ $action->act_details }}</p>
                            
                            @if($action->act_file)
                                <a href="{{ asset('storage/' . $action->act_file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-paperclip me-1"></i> View Attached Evidence
                                </a>
                            @endif
                        </div>

                        <!-- Update form route to point to the action ID if you verify by action, or keep tck_id -->
                        <form action="{{ route('workflow.verify', $action->tck_id) }}" method="POST" class="mt-4">
                            @csrf
                            <!-- If your route uses Action ID instead of Ticket ID, change the route param to $action->act_id -->
                            <input type="hidden" name="act_id" value="{{ $action->act_id }}">
                            
                            <div class="bg-light p-3 rounded border">
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label fw-bold small">Supervisor Remarks (Required if rejecting)</label>
                                        <textarea name="remarks" class="form-control" rows="2" placeholder="Explain why this resolution is accepted or rejected..."></textarea>
                                    </div>

                                    <div class="col-md-12 text-end">
                                        <button name="status" value="reject" class="btn btn-outline-danger me-2" onclick="return confirm('Return this to the department for revision?')">
                                            <i class="fas fa-times me-1"></i> Disapprove
                                        </button>
                                        <button name="status" value="accept" class="btn btn-success px-4" onclick="return confirm('Finalize and close this ticket?')">
                                            <i class="fas fa-check-circle me-1"></i> Approve & Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-clipboard-check fa-4x text-light mb-3"></i>
            <h4 class="text-muted">All caught up!</h4>
            <p>No actions are currently waiting for final verification.</p>
        </div>
    @endforelse
</div>

<style>
    .bg-maroon { background-color: maroon; color: white; }
    .text-maroon { color: maroon; }
    .border-maroon { border-color: maroon; }
</style>
@endsection