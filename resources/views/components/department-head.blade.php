<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="text-secondary fw-bold mb-1">Department Desk Overview</h5>
            <span class="text-muted small">Monitor ticket queues across your assigned departments</span>
        </div>
        <a  class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
            Open Action Queue ➡️
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Action Required</h6>
                    <h2 class="mb-0 text-dark">{{ $actionCount ?? 0 }}</h2>
                    <small class="text-muted">Tickets awaiting response</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100 bg-danger bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-uppercase text-danger fw-bold mb-1">Overdue / SLA Breach</h6>
                    <h2 class="mb-0 text-danger">{{ $urgentActionCount ?? 0 }}</h2>
                    <small class="text-danger fw-medium">Unanswered > 24 Hours</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Resolved Today</h6>
                    <h2 class="mb-0 text-dark">{{ $actionedTodayCount ?? 0 }}</h2>
                    <small class="text-muted">Responses provided by you</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 border-0">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase">Queue by Department</h6>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex flex-column gap-3">
                        @forelse($myDepartments ?? [] as $dept)
                            @php
                                // Calculate severity color based on pending count
                                $badgeColor = $dept->pending_tickets_count > 10 ? 'bg-danger' : 
                                             ($dept->pending_tickets_count > 0 ? 'bg-warning text-dark' : 'bg-success');
                            @endphp
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fs-4 opacity-50">🏢</div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $dept->dep_name }}</span>
                                        <span class="text-muted small">Department Workload</span>
                                    </div>
                                </div>
                                <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2 shadow-sm">
                                    {{ $dept->pending_tickets_count }} Pending
                                </span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small">
                                You are not assigned to any departments.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 border-0">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase">My Recent Responses</h6>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex flex-column gap-3">
                        @forelse($recentActions ?? [] as $recent)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                <div>
                                    <span class="fw-bold text-primary">Ticket #{{ $recent->tck_id }}</span>
                                    <span class="text-muted small d-block">{{ $recent->department->dep_name ?? 'Unknown' }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Responded</span>
                                    <div class="text-muted" style="font-size: 0.7rem; margin-top: 2px;">
                                        {{ $recent->tck_date_action ? $recent->tck_date_action->diffForHumans() : '' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small">
                                No responses provided yet today.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>