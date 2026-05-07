<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Feedback History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 900px;">
    
    <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="fa fa-folder-open me-2"></i> Feedback History</h3>
                <p class="mb-0 text-white-50">Viewing records for: <strong>{{ $email }}</strong></p>
            </div>
            <div class="text-end">
                <span class="badge bg-light text-primary py-2 px-3">Secure Session</span>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="fa fa-info-circle fs-4 me-3"></i>
        <div>This is a secure, temporary link. For your privacy, it will expire 24 hours after it was generated.</div>
    </div>

    <div class="row g-4">
        @forelse($feedbacks as $fb)
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="fa fa-calendar-alt me-1"></i> Submitted: {{ $fb->fbk_date_created->format('M d, Y h:i A') }}
                        </span>
                        
                        @if($fb->tickets->isEmpty() && $fb->fbk_status == 0)
                            <span class="badge bg-secondary">Pending Validation</span>
                        @elseif($fb->tickets->first())
                            @php
                                $status = $fb->tickets->first()->tck_status;
                            @endphp
                            <span class="badge {{ $status == 'resolved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ strtoupper($status ?? 'In Progress') }}
                            </span>
                        @else
                            <span class="badge bg-dark">Archived</span>
                        @endif
                    </div>
                    
                    <div class="card-body">
                        <p class="fs-5 text-dark mb-3">"{{ Str::limit($fb->fbk_details, 150) }}"</p>
                        
                        @if($fb->tickets->first() && $fb->tickets->first()->department)
                            <div class="text-muted small mb-3">
                                <i class="fa fa-building me-1"></i> Assigned to: <strong>{{ $fb->tickets->first()->department->dep_name }}</strong>
                            </div>
                        @endif
                        
                        
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fa fa-inbox fs-1"></i>
                </div>
                <h5 class="fw-bold">No feedback found</h5>
                <p class="text-muted">We couldn't find any feedback associated with this email address.</p>
            </div>
        @endforelse
    </div>

</div>

</body>
</html>