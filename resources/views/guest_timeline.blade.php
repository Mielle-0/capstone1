@extends('layouts.guest')

@section('title', 'Feedback Timeline')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0"><i class="fas fa-stream text-maroon me-2"></i> Feedback Timeline</h3>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-chevron-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-5 border-start border-4 border-maroon">
        <div class="card-body bg-white rounded">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="small fw-bold text-muted text-uppercase">Original Submission</span>
                <span class="text-muted small">{{ $feedback->fbk_date_created->format('M d, Y h:i A') }}</span>
            </div>
            <p class="mb-0 fs-5 fst-italic text-dark">"{{ $feedback->fbk_details }}"</p>
        </div>
    </div>

    @if($feedback->tickets->isNotEmpty())
        @foreach($feedback->tickets as $ticket)
            <div class="mb-5">
                <h5 class="fw-bold text-maroon mb-3 border-bottom pb-2">
                    <i class="fas fa-building me-2"></i> {{ $ticket->department->dep_name ?? 'Department' }}
                    
                    @if($ticket->tck_rate_date)
                        <span class="badge bg-success small float-end fs-6">Resolved</span>
                    @else
                        <span class="badge bg-secondary small float-end fs-6">In Progress</span>
                    @endif
                </h5>

                @php
                    // Combine verified actions and guest responses into a single threaded timeline
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
                    <div class="alert alert-light border text-center text-muted py-4">
                        <i class="fas fa-hourglass-half fs-3 mb-2"></i>
                        <p class="mb-0">The department is currently reviewing your feedback. Updates will appear here.</p>
                    </div>
                @else
                    <div class="ms-3 ps-4 border-start border-3 border-maroon position-relative" style="border-color: #800000 !important;">
                        @foreach($thread as $item)
                            
                            {{-- DEPARTMENT ACTION --}}
                            @if($item['type'] == 'action')
                                <div class="mb-4 position-relative">
                                    <div class="position-absolute bg-maroon rounded-circle" style="width: 12px; height: 12px; left: -31px; top: 5px; background-color: #800000;"></div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="text-maroon"><i class="fas fa-tools me-1"></i> Department Response</strong>
                                        <small class="text-muted">{{ $item['date']->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <div class="p-3 bg-white border rounded shadow-sm">
                                        {{ $item['model']->act_details }}
                                    </div>
                                </div>
                            
                            {{-- GUEST REPLY --}}
                            @elseif($item['type'] == 'response')
                                <div class="mb-4 position-relative pe-3 ms-4"> 
                                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -55px; top: 5px;"></div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="text-primary"><i class="fas fa-user me-1"></i> You</strong>
                                        <small class="text-muted">{{ $item['date']->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <div class="p-3 bg-light border border-primary rounded shadow-sm fst-italic">
                                        "{{ $item['model']->res_message }}"
                                    </div>
                                </div>
                            @endif

                        @endforeach
                    </div>
                @endif

                {{-- GUEST REPLY & RATING FORM --}}
                @php
                    $lastItem = $thread->last();
                    // Show form only if the last item is a department action and the ticket hasn't been rated
                    $canReply = $lastItem && $lastItem['type'] == 'action' && is_null($ticket->tck_rate_date);
                @endphp

                @if($canReply)
                    <div class="mt-4 ms-4 p-4 bg-white border border-primary rounded shadow-sm">
                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-tasks me-1"></i> Action Required: Choose your next step</h6>
                        
                        {{-- The Two Buttons --}}
                        <div class="d-flex gap-2 mb-3" id="guestActionButtons">
                            <button class="btn btn-success flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRate" aria-expanded="false">
                                <i class="fas fa-star me-1"></i> Rate and Close Feedback
                            </button>
                            <button class="btn btn-outline-primary flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRespond" aria-expanded="false">
                                <i class="fas fa-reply me-1"></i> Respond
                            </button>
                        </div>

                        {{-- Container to ensure only one form shows at a time --}}
                        <div id="actionFormsContainer">
                            
                            {{-- FORM 1: RATE & CLOSE --}}
                            <div class="collapse" id="collapseRate" data-bs-parent="#actionFormsContainer">
                                <form action="{{ route('feedback.guest.rate', ['id' => $ticket->tck_id]) }}" method="POST" class="p-3 bg-light border rounded">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-1">Rate the Resolution <span class="text-danger">*</span></label>
                                        <p class="small text-muted mb-2">Submitting a rating will officially close this ticket.</p>
                                        <select name="tck_rate" class="form-select w-auto" required>
                                            <option value="" disabled selected>Select Rating...</option>
                                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                                            <option value="4">⭐⭐⭐⭐ (4 - Good)</option>
                                            <option value="3">⭐⭐⭐ (3 - Average)</option>
                                            <option value="2">⭐⭐ (2 - Poor)</option>
                                            <option value="1">⭐ (1 - Terrible)</option>
                                        </select>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success px-4" onclick="return confirm('Close this ticket with your rating?')">
                                            <i class="fas fa-check-circle me-1"></i> Submit Rating & Close
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- FORM 2: RESPOND --}}
                            <div class="collapse" id="collapseRespond" data-bs-parent="#actionFormsContainer">
                                <form action="{{ route('feedback.guest.reply', ['id' => $ticket->tck_id]) }}" method="POST" class="p-3 bg-light border rounded">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-1">Reply to Department <span class="text-danger">*</span></label>
                                        <p class="small text-muted mb-2">This will send the ticket back to the department for further review.</p>
                                        <textarea name="res_message" class="form-control" rows="3" placeholder="Write your response..." required></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Send response to department?')">
                                            <i class="fas fa-paper-plane me-1"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                @endif
                
                @if($ticket->tck_rate_date && $ticket->tck_rate)
                    <div class="mt-4 ms-3 p-3 bg-light border border-success rounded text-center shadow-sm">
                        <h6 class="text-success fw-bold mb-1"><i class="fas fa-check-circle me-1"></i> Ticket Resolved & Closed</h6>
                        <p class="small text-muted mb-2">Closed on {{ \Carbon\Carbon::parse($ticket->tck_rate_date)->format('M d, Y h:i A') }}</p>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6 shadow-sm">
                                Your Rating: {{ str_repeat('⭐', $ticket->tck_rate) }} ({{ $ticket->tck_rate }}/5)
                            </span>
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    @endif

</div>
@endsection