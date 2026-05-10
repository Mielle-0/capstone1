@extends('layouts.guest') 

@section('title', 'My Feedback History')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    
    <div class="card shadow-sm border-0 mb-4 bg-maroon text-white" style="background-color: #800000;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="fas fa-folder-open me-2"></i> Feedback History</h3>
                <p class="mb-0 text-white-50">Viewing records for: <strong>{{ $email }}</strong></p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-maroon py-2 px-3 shadow-sm"><i class="fas fa-lock me-1"></i> Secure Session</span>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="fas fa-info-circle fs-4 me-3"></i>
        <div>This is a secure, temporary link. For your privacy, it will expire 24 hours after it was generated.</div>
    </div>

    <div class="row g-4">
        @forelse($feedbacks as $fb)
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-maroon">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">
                            <i class="fas fa-calendar-alt me-1"></i> Submitted: {{ $fb->fbk_date_created->format('M d, Y h:i A') }}
                        </span>
                        
                        {{-- CORRECTED STATUS LOGIC --}}
                        @if($fb->fbk_status === 0)
                            <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending Validation</span>
                        @elseif($fb->fbk_status === 2)
                            <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Dropped</span>
                        @elseif($fb->fbk_status === 1)
                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Approved & Routed</span>
                        @endif
                    </div>
                    
                    <div class="card-body">
                        <p class="fs-6 text-dark mb-3 fst-italic">"{{ Str::limit($fb->fbk_details, 200) }}"</p>
                        
                        {{-- IF ROUTED TO DEPARTMENTS, SHOW TICKETS --}}
                        @if($fb->tickets->isNotEmpty())
                            <hr class="text-muted">
                            <h6 class="small fw-bold text-uppercase text-muted mb-2">Routed Departments</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($fb->tickets as $ticket)
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light border rounded">
                                        <span class="small fw-bold text-dark">
                                            <i class="fas fa-building text-maroon me-1"></i> {{ $ticket->department->dep_name ?? 'Department' }}
                                        </span>
                                        
                                        {{-- TICKET STATUS LOGIC --}}
                                        @if($ticket->tck_rate_date)
                                            <span class="badge bg-success small">Resolved</span>
                                        @elseif($ticket->tck_date_action)
                                            <span class="badge bg-info text-dark small">Pending Verification</span>
                                        @else
                                            <span class="badge bg-secondary small">In Progress</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-footer bg-white border-top-0 pb-3 text-end">
                        <a href="{{ URL::temporarySignedRoute('feedback.guest.timeline', now()->addHours(24), ['email' => $email, 'id' => $fb->fbk_id]) }}" class="btn btn-sm btn-outline-maroon" style="color: #800000; border-color: #800000;">
                            View Full Details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-inbox fs-1"></i>
                </div>
                <h5 class="fw-bold">No feedback found</h5>
                <p class="text-muted">We couldn't find any feedback associated with this email address.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection