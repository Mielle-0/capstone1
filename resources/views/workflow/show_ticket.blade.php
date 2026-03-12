@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ticket Details <small class="text-muted">#{{ substr($ticket->tck_uuid, 0, 8) }}</small></h2>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Original Feedback</div>
                <div class="card-body">
                    <p><strong>Sender:</strong> {{ $ticket->feedback->std_name }}</p>
                    <p><strong>Department:</strong> {{ $ticket->department->dep_name ?? 'Unassigned' }}</p>
                    <p><strong>Type:</strong> {{ $ticket->feedback->type->typ_name ?? 'N/A' }}</p>
                    <hr>
                    <p class="font-italic">"{{ $ticket->feedback->fbk_details }}"</p>
                    <small class="text-muted">Received: {{ $ticket->feedback->fbk_date_created->format('M d, Y h:i A') }}</small>
                </div>
            </div>

            @if($ticket->tck_date_verified)
                <div class="card border-success shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-success">Ticket Resolved</h5>
                        <div class="h2">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $ticket->tck_rate ? 'text-warning' : 'text-muted' }}">★</span>
                            @endfor
                        </div>
                        <p class="text-muted small">Rated on {{ $ticket->tck_rate_date->format('M d, Y') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <h4 class="mb-3">Audit Trail (Action History)</h4>
            
            <div class="timeline-container">
                @forelse($auditTrail as $action)
                    <div class="card mb-3 border-left-{{ $action->act_status == 1 ? 'success' : ($action->act_status == 2 ? 'danger' : 'info') }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-bold">
                                    {{ $action->creator->usr_name ?? 'System' }} 
                                    <span class="badge badge-pill {{ $action->act_status == 1 ? 'badge-success' : ($action->act_status == 2 ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $action->act_status == 1 ? 'Verified' : ($action->act_status == 2 ? 'Rejected' : 'Pending Verification') }}
                                    </span>
                                </h6>
                                <small class="text-muted">{{ $action->act_date_created->format('M d, Y h:i A') }}</small>
                            </div>

                            <p class="mt-2">{{ $action->act_details }}</p>

                            @if($action->act_file)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $action->act_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        📎 View Attachment
                                    </a>
                                </div>
                            @endif

                            @if($action->act_status == 2)
                                <div class="alert alert-danger mt-3 py-2">
                                    <strong>Rejection Reason:</strong> {{ $action->act_reject_details }}
                                    <div class="small">By: {{ $action->verifier->usr_name ?? 'Supervisor' }}</div>
                                </div>
                            @endif

                            @if($action->act_status == 1)
                                <div class="text-success mt-2 small">
                                    ✓ Verified by {{ $action->verifier->usr_name }} on {{ $action->act_date_verified->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light text-center">No actions have been recorded yet.</div>
                @endforelse

                <div class="text-center my-4">
                    <span class="badge badge-secondary">Ticket Created: {{ $ticket->tck_date_created->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-success { border-left: 5px solid #28a745 !important; }
    .border-left-danger { border-left: 5px solid #dc3545 !important; }
    .border-left-info { border-left: 5px solid #17a2b8 !important; }
    .timeline-container { position: relative; padding-left: 20px; border-left: 2px dashed #dee2e6; }
</style>
@endsection