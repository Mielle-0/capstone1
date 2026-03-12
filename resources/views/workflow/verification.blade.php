@extends('layouts.app')

@section('content')
<div class="container py-4">

    @forelse($tickets as $t)
        @php
            // Get the very latest action submitted by the department
            $latestAction = $t->actions->first(); 
        @endphp

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="badge bg-maroon">Ticket #{{ $t->tck_id }}</span>
                <small class="text-muted">Assigned Dept: <strong>{{ $t->department->dep_name ?? 'N/A' }}</strong></small>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 border-end">
                        <h6 class="text-uppercase small fw-bold text-muted">Original Feedback</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <strong>{{ $t->feedback->std_name }}</strong> <small>({{ $t->feedback->std_program }})</small>
                            <p class="mt-2 mb-0 font-italic">"{{ $t->feedback->fbk_details }}"</p>
                        </div>
                        <small class="text-muted">Submitted: {{ $t->feedback->fbk_date_created->format('M d, Y h:i A') }}</small>
                    </div>

                    <div class="col-md-7 ps-md-4">
                        <h6 class="text-uppercase small fw-bold text-success">Department Resolution</h6>
                        
                        @if($latestAction)
                            <div class="p-3 border border-success rounded bg-white shadow-sm mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-primary">{{ $latestAction->creator->usr_name ?? 'Staff' }}</span>
                                    <small class="text-muted">{{ $latestAction->act_date_created->format('M d, Y h:i A') }}</small>
                                </div>
                                <p class="mb-2">{{ $latestAction->act_details }}</p>
                                
                                @if($latestAction->act_file)
                                    <a href="{{ asset('storage/' . $latestAction->act_file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-paperclip me-1"></i> View Attached Evidence
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning">Warning: No action details found.</div>
                        @endif

                        <form action="{{ route('workflow.verify', $t->tck_id) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="bg-light p-3 rounded border">
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label fw-bold small">Supervisor Remarks (Required if rejecting)</label>
                                        <textarea name="remarks" class="form-control" rows="2" placeholder="Explain why this resolution is accepted or rejected..."></textarea>
                                    </div>
                                    
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold small text-maroon">User Satisfaction Rate</label>
                                        <select name="tck_rate" class="form-select border-maroon">
                                            <option value="5">Excellent (5)</option>
                                            <option value="4">Good (4)</option>
                                            <option value="3" selected>Average (3)</option>
                                            <option value="2">Poor (2)</option>
                                            <option value="1">Terrible (1)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-7 text-end d-flex align-items-end justify-content-end">
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
            <p>No tickets are currently waiting for final verification.</p>
        </div>
    @endforelse
</div>

<style>
    .bg-maroon { background-color: maroon; color: white; }
    .text-maroon { color: maroon; }
    .border-maroon { border-color: maroon; }
</style>
@endsection