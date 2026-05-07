<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="text-secondary fw-bold mb-1">Verification Desk Overview</h5>
            <span class="text-muted small">Quality assurance for department responses</span>
        </div>
        <a  class="btn btn-info text-white px-4 py-2 shadow-sm fw-bold">
            Open Verification Queue ➡️
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Pending Review</h6>
                    <h2 class="mb-0 text-dark">{{ $verificationCount ?? 0 }}</h2>
                    <small class="text-muted">Responses awaiting QA</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 bg-danger bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-uppercase text-danger fw-bold mb-1">Aging Responses</h6>
                    <h2 class="mb-0 text-danger">{{ $urgentVerificationCount ?? 0 }}</h2>
                    <small class="text-danger fw-medium">Unverified > 24 Hours</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Verified Today</h6>
                    <h2 class="mb-0 text-dark">{{ $verifiedTodayCount ?? 0 }}</h2>
                    <small class="text-muted">Responses approved</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 border-0">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase">Pending Responses by Dept</h6>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex flex-column gap-3 overflow-auto pe-2" style="max-height: 300px;">
                        @forelse($verificationByDept ?? [] as $deptGroup)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fs-5 opacity-50 text-info">📂</div>
                                    <span class="fw-medium text-dark">{{ $deptGroup->dep_name ?? 'Unknown Dept' }}</span>
                                </div>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">
                                    {{ $deptGroup->total }} Responses
                                </span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small">
                                <div class="fs-1 mb-2 opacity-25">✨</div>
                                No responses waiting for verification!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 border-0">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase">My Recent Approvals</h6>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex flex-column gap-3 overflow-auto pe-2" style="max-height: 300px;">
                        @forelse($recentVerifications ?? [] as $recent)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                <div>
                                    <span class="fw-bold text-dark">Ticket #{{ $recent->ticket->tck_id ?? 'N/A' }}</span>
                                    <span class="text-muted small d-block">Action #{{ $recent->act_id }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success text-white">Verified</span>
                                    <div class="text-muted" style="font-size: 0.7rem; margin-top: 2px;">
                                        {{ $recent->act_date_verified ? $recent->act_date_verified->format('M d, h:i A') : '' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small">
                                No actions verified yet today.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>