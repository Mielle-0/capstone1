<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="text-secondary fw-bold mb-1">Validation Desk Overview</h5>
            <span class="text-muted small">Current queue health and daily metrics</span>
        </div>
        <a href="{{ route('workflow.validation') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
            Open Validation Queue ➡️
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Pending Total</h6>
                    <h2 class="mb-0 text-dark">{{ $validationCount ?? 0 }}</h2>
                    <small class="text-muted">Awaiting your review</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 bg-danger bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-uppercase text-danger fw-bold mb-1">Urgent / Overdue</h6>
                    <h2 class="mb-0 text-danger">{{ $urgentCount ?? 0 }}</h2>
                    <small class="text-danger fw-medium">Pending > 24 Hours</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Cleared Today</h6>
                    <h2 class="mb-0 text-dark">{{ $validatedTodayCount ?? 0 }}</h2>
                    <small class="text-muted">Routed to departments</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 border-0">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase">Pending by Feedback Type</h6>
                </div>
                <div class="card-body pt-2">
                    @forelse($pendingByType ?? [] as $group)
                        @php
                            // Calculate percentage for the progress bar
                            $percentage = ($validationCount > 0) ? ($group->total / $validationCount) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="fw-medium text-dark">{{ $group->type->typ_value ?? 'Uncategorized' }}</span>
                                <span class="text-muted small">{{ $group->total }} tickets</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            Queue is completely empty. Great job!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 border-0">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase">Pending by Branch</h6>
                </div>
                <div class="card-body pt-2">
                    @forelse($pendingByBranch ?? [] as $branchGroup)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <span class="fw-medium text-dark">{{ $branchGroup->branch->branch_id ?? 'Unknown' }}</span>
                            <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $branchGroup->total }} Pending</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4 small">
                            Queue empty.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>