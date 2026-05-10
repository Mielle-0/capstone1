@extends('layouts.app')

@section('title', 'Feedback Timeline')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Feedback Lifecycle</h3>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-chevron-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body bg-light rounded">
            <div class="row">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase">Sender</label>
                    <p class="mb-0 fw-bold fs-5">{{ $feedback->std_name }}</p>
                    <p class="text-muted small">{{ $feedback->std_id_no }} | {{ $feedback->std_email }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <label class="small fw-bold text-muted text-uppercase">Feedback Status</label>
                    <p class="mb-0 fw-bold fs-5">
                        @if($feedback->fbk_status === 0)
                            <span class="text-warning"><i class="fas fa-clock"></i> Pending Validation</span>
                        @elseif($feedback->fbk_status === 1)
                            <span class="text-success"><i class="fas fa-check-circle"></i> Approved & Ticketed</span>
                        @elseif($feedback->fbk_status === 2)
                            <span class="text-danger"><i class="fas fa-times-circle"></i> Dropped</span>
                        @endif
                    </p>
                </div>
            </div>
            <hr>
            <label class="small fw-bold text-muted text-uppercase">Original Message</label>
            <p class="mb-0 fst-italic">"{{ $feedback->fbk_details }}"</p>
        </div>
    </div>

    <h4 class="fw-bold mb-4 text-maroon"><i class="fas fa-stream me-2"></i> Timeline</h4>
    
    <div class="timeline">
        
        <div class="timeline-item">
            <div class="timeline-icon bg-white text-maroon border-maroon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="timeline-content shadow-sm">
                <div class="d-flex justify-content-between">
                    <h6 class="fw-bold mb-1">Feedback Submitted</h6>
                    <small class="text-muted">{{ $feedback->fbk_date_created->format('M d, Y h:i A') }}</small>
                </div>
                <p class="mb-0 small text-muted">Feedback received from student.</p>
            </div>
        </div>

        @if($feedback->fbk_date_validated)
            <div class="timeline-item">
                <div class="timeline-icon bg-white {{ $feedback->fbk_status == 1 ? 'text-success border-success' : 'text-danger border-danger' }}">
                    <i class="fas {{ $feedback->fbk_status == 1 ? 'fa-check' : 'fa-times' }}"></i>
                </div>
                <div class="timeline-content shadow-sm">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold mb-1">
                            {{ $feedback->fbk_status == 1 ? 'Validated & Approved' : 'Feedback Dropped' }}
                        </h6>
                        <small class="text-muted">{{ $feedback->fbk_date_validated->format('M d, Y h:i A') }}</small>
                    </div>
                    <p class="mb-1 small text-muted">Action taken by: <strong>{{ $feedback->validator->name ?? 'System/Unknown' }}</strong></p>
                    
                    @if($feedback->fbk_status == 2 && $feedback->fbk_disapprove_details)
                        <div class="alert alert-danger mt-2 mb-0 py-2 small">
                            <strong>Reason:</strong> {{ $feedback->fbk_disapprove_details }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($feedback->fbk_status == 1 && $feedback->tickets->isNotEmpty())
            @foreach($feedback->tickets as $ticket)
                <div class="timeline-item">
                    <div class="timeline-icon bg-maroon text-white border-maroon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="timeline-content shadow-sm border-start border-4 border-maroon">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-1">Routed to {{ $ticket->department->dep_name ?? 'Department' }}</h6>
                            <small class="text-muted">{{ $ticket->tck_date_created->format('M d, Y h:i A') }}</small>
                        </div>
                        <p class="mb-2 small text-muted">Ticket ID: <strong>{{ $ticket->tck_uuid }}</strong></p>

                        @php
                            // Combine actions and responses into one collection, then sort them by date
                            $thread = collect();
                            
                            foreach($ticket->actions as $action) {
                                $thread->push([
                                    'type' => 'action', 
                                    'date' => $action->act_date_created, 
                                    'model' => $action
                                ]);
                            }
                            foreach($ticket->responses as $response) {
                                $thread->push([
                                    'type' => 'response', 
                                    'date' => $response->res_date_created, 
                                    'model' => $response
                                ]);
                            }
                            
                            // Sort oldest to newest
                            $thread = $thread->sortBy('date');
                        @endphp

                        @if($thread->isEmpty())
                            @if(auth()->user()->hasAnyRole(['Department Head']))
                                <div class="mt-3 p-3 bg-white border border-maroon rounded shadow-sm">
                                    <form action="{{ route('workflow.submit_action', $ticket->tck_id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="small text-muted text-uppercase fw-bold mb-1">Action Taken <span class="text-danger">*</span></label>
                                        <textarea name="details" class="form-control mb-3" rows="3" placeholder="Describe the resolution..." required></textarea>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="w-50">
                                                <input type="file" name="act_file" class="form-control form-control-sm" accept=".pdf,.jpg,.png,.docx">
                                            </div>
                                            <button type="submit" class="btn btn-maroon px-4 shadow-sm" onclick="return confirm('Submit this action?')">
                                                <i class="fas fa-paper-plane me-1"></i> Submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half"></i> Pending Activity</span>
                            @endif    
                        @else
                            <div class="mt-3 ps-3 border-start border-2">
                                @foreach($thread as $item)
                                    
                                    @if($item['type'] == 'action')
                                        <div class="mb-3 ms-2">
                                            <div class="d-flex justify-content-between">
                                                <strong class="small text-maroon"><i class="fas fa-tools me-1"></i> Department Action</strong>
                                                <small class="text-muted">{{ $item['date']->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <div class="small mb-1 mt-1 bg-white p-2 border border-maroon rounded">
                                                {{ $item['model']->act_details }}
                                            </div>
                                            <p class="small text-muted mb-1">- by {{ $item['model']->creator->usr_name ?? 'Staff' }}</p>

                                            @if($item['model']->act_status == 1)
                                                <div class="text-success small fw-bold mt-1">
                                                    <i class="fas fa-check-double"></i> Verified by {{ $item['model']->verifier->usr_name ?? 'Verifier' }}
                                                </div>
                                            @elseif($item['model']->act_status == 2)
                                                <div class="text-danger small mt-1">
                                                    <i class="fas fa-exclamation-circle"></i> <strong>Rejected by {{ $item['model']->verifier->usr_name ?? 'Verifier' }}:</strong> {{ $item['model']->act_reject_details }}
                                                </div>
                                            @elseif($item['model']->act_status == 0)
                                                <div class="text-warning small fw-bold mt-1 mb-2">
                                                    <i class="fas fa-user-clock"></i> Pending Verification
                                                </div>
                                                
                                                {{-- Verifier Action Form --}}
                                                @if(auth()->user()->hasAnyRole(['Verifier']))
                                                    <div class="mt-2 p-3 bg-light border border-secondary rounded">
                                                        <form action="{{ route('workflow.verify', $ticket->tck_id) }}" method="POST">
                                                            @csrf
                                                            <label class="small fw-bold text-muted text-uppercase mb-1">Verifier Decision</label>
                                                            <textarea name="remarks" class="form-control form-control-sm mb-2" rows="2" placeholder="Provide a reason if rejecting..."></textarea>
                                                            
                                                            <div class="d-flex gap-2">
                                                                <button type="submit" name="status" value="accept" class="btn btn-sm btn-success px-3" onclick="return confirm('Accept this action?')">
                                                                    <i class="fas fa-check me-1"></i> Accept & Verify
                                                                </button>
                                                                <button type="submit" name="status" value="reject" class="btn btn-sm btn-danger px-3" onclick="return confirm('Reject this action?')">
                                                                    <i class="fas fa-times me-1"></i> Reject
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    
                                    {{-- SENDER RESPONSE --}}
                                    @elseif($item['type'] == 'response')
                                        <div class="mb-3 ms-4 pe-2"> {{-- Pushed further right to look like a chat reply --}}
                                            <div class="d-flex justify-content-between">
                                                <strong class="small text-primary"><i class="fas fa-user-edit me-1"></i> Sender Reply</strong>
                                                <small class="text-muted">{{ $item['date']->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <div class="small mb-1 mt-1 bg-light p-2 border border-primary rounded" style="font-style: italic;">
                                                "{{ $item['model']->res_message }}"
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- NEW: Follow-up Action Form for Department Head --}}
                                @if(is_null($ticket->tck_rate_date) && auth()->user()->hasAnyRole(['Department Head']))
                                    @php
                                        $latestItem = $thread->last();
                                        $needsFollowUp = false;
                                        
                                        if ($latestItem) {
                                            // Check if the latest item is a response OR a rejected action
                                            if ($latestItem['type'] == 'response') {
                                                $needsFollowUp = true;
                                            } elseif ($latestItem['type'] == 'action' && $latestItem['model']->act_status == 2) {
                                                $needsFollowUp = true;
                                            }
                                        }
                                    @endphp

                                    @if($needsFollowUp)
                                        <div class="mt-4 ms-2 p-3 bg-white border border-maroon rounded shadow-sm">
                                            <h6 class="small text-maroon fw-bold mb-2"><i class="fas fa-reply me-1"></i> Add Follow-up Action</h6>
                                            <form action="{{ route('workflow.submit_action', $ticket->tck_id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <label class="small text-muted text-uppercase fw-bold mb-1">Action Taken <span class="text-danger">*</span></label>
                                                <textarea name="details" class="form-control mb-3" rows="3" placeholder="Describe the next step or resolution..." required></textarea>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="w-50">
                                                        <input type="file" name="act_file" class="form-control form-control-sm" accept=".pdf,.jpg,.png,.docx">
                                                    </div>
                                                    <button type="submit" class="btn btn-maroon px-4 shadow-sm" onclick="return confirm('Submit this action?')">
                                                        <i class="fas fa-paper-plane me-1"></i> Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                        @if($ticket->tck_rate_date && $ticket->tck_rate)
                            <div class="mt-4 p-3 bg-light border border-success rounded text-center shadow-sm">
                                <h6 class="text-success fw-bold mb-1"><i class="fas fa-check-circle me-1"></i> Ticket Resolved & Closed</h6>
                                <p class="small text-muted mb-2">Closed on {{ \Carbon\Carbon::parse($ticket->tck_rate_date)->format('M d, Y h:i A') }}</p>
                                <div>
                                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 shadow-sm">
                                        Rating: {{ str_repeat('⭐', $ticket->tck_rate) }} ({{ $ticket->tck_rate }}/5)
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>

<style>
    /* Custom Timeline CSS */
    .text-maroon { color: #800000; }
    .bg-maroon { background-color: #800000; }
    .border-maroon { border-color: #800000 !important; }

    .timeline {
        border-left: 3px solid #e9ecef;
        position: relative;
        padding-left: 2rem;
        margin-left: 1rem;
        list-style: none;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2.5rem;
    }
    .timeline-icon {
        position: absolute;
        left: -3.15rem;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid;
        font-size: 1.1rem;
        z-index: 1;
    }
    .timeline-content {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
    }
</style>
@endsection