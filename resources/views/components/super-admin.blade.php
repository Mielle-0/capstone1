<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h5 class="text-secondary fw-bold mb-1">User Management</h5>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary px-3 shadow-sm">Manage Users</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Total Users</h6>
                        <h3 class="mb-0 text-dark">{{ $totalUsers ?? 0 }}</h3>
                    </div>
                    <div class="fs-2 opacity-25">👥</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Active Accounts</h6>
                        <h3 class="mb-0 text-dark">{{ $activeUsers ?? 0 }}</h3>
                    </div>
                    <div class="fs-2 opacity-25">✅</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-secondary border-4 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Inactive Accounts</h6>
                        <h3 class="mb-0 text-dark">{{ $inactiveUsers ?? 0 }}</h3>
                    </div>
                    <div class="fs-2 opacity-25">🔒</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">Active Role Distribution</h6>
                    <div class="d-flex flex-column gap-2 overflow-auto pe-2" style="max-height: 250px;">
                        @forelse($roleDistribution ?? [] as $role)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 pt-1">
                                <span class="text-dark fw-medium">{{ $role->rol_name }}</span>
                                <span class="badge bg-light text-dark border rounded-pill px-3">{{ $role->users_count }}</span>
                            </div>
                        @empty
                            <span class="text-muted small">No roles found.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">Recently Onboarded</h6>
                    <div class="d-flex flex-column gap-3 overflow-auto pe-2" style="max-height: 250px;">
                        @forelse($recentUsers ?? [] as $recentUser)
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 38px; height: 38px;">
                                        {{ substr($recentUser->usr_name, 0, 1) }}
                                    </div>
                                    <div class="lh-sm">
                                        <div class="fw-semibold text-dark">{{ $recentUser->usr_name }}</div>
                                        <div class="text-muted small">
                                            {{ $recentUser->roles->pluck('rol_name')->join(', ') ?: 'No Roles' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge {{ $recentUser->usr_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill">
                                    {{ $recentUser->usr_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @empty
                            <span class="text-muted small">No recent users.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="text-secondary fw-bold mb-1">AI Routing Performance & Triage</h5>
        </div>
        <a href="/admin/ai-settings" class="btn btn-sm btn-outline-dark px-3">Configure AI</a>
    </div> -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h5 class="text-secondary fw-bold mb-1">AI Routing Performance & Triage</h5>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <!-- Date Dropdown Popover -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light border shadow-sm dropdown-toggle fw-semibold text-secondary px-3" type="button" id="dateFilterDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                {{ request('ml_date_from') ? \Carbon\Carbon::parse(request('ml_date_from'))->format('M d') . ' - ' . \Carbon\Carbon::parse(request('ml_date_to'))->format('M d, Y') : 'Last 30 Days' }}
            </button>
            
            <form action="{{ route('dashboard') }}" method="GET" class="dropdown-menu dropdown-menu-end p-3 shadow border-0 mt-2" style="width: 280px;" aria-labelledby="dateFilterDropdown">
                <div class="mb-2">
                    <label class="form-label small text-muted fw-bold mb-1">From Date</label>
                    <input type="date" name="ml_date_from" class="form-control form-control-sm" value="{{ request('ml_date_from', now()->subDays(30)->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold mb-1">To Date</label>
                    <input type="date" name="ml_date_to" class="form-control form-control-sm" value="{{ request('ml_date_to', now()->format('Y-m-d')) }}">
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-link text-decoration-none text-muted p-0">Reset</a>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Apply</button>
                </div>
            </form>
        </div>

        <a href="/admin/ai-settings" class="btn btn-sm btn-outline-dark px-3 text-nowrap">Configure AI</a>
    </div>
</div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-dark text-white h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="text-uppercase text-white-50 fw-bold mb-2">Model Accuracy (All Time)</h6>
                    <div class="d-flex align-items-baseline gap-2">
                        <h1 class="mb-0 fw-bold">{{ $aiSuccessRate ?? '0' }}%</h1>
                        <span class="badge {{ ($aiSuccessRate ?? 0) >= 80 ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $totalAiRouted ?? 0 }} Verifications
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">System Parameters</h6>
                    <div class="row text-center">
                        <div class="col-3 border-end">
                            <small class="text-muted d-block text-uppercase mb-1">Model Version</small>
                            <span class="fs-5 fw-bold text-dark">{{ $currentAiVersion ?? 'v1.0' }}</span>
                        </div>
                        <div class="col-3 border-end">
                            <small class="text-muted d-block text-uppercase mb-1">Threshold</small>
                            <span class="fs-5 fw-bold text-dark">{{ $currentThreshold }}%</span>
                        </div>
                        <div class="col-3 border-end">
                            <small class="text-muted d-block text-uppercase mb-1">Total Returns</small>
                            <span class="fs-5 fw-bold text-danger">{{ $totalMisclassifications ?? 0 }}</span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block text-uppercase mb-1">Status</small>
                            @if(isset($aiApiStatus) && $aiApiStatus === 'Online')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                    <i class="fas fa-circle small me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Online
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill" title="Could not connect to {{ config('services.ml_api.url') }}">
                                    <i class="fas fa-times-circle me-1"></i> Offline
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4">
                    <h6 class="mb-0 fw-bold text-muted text-uppercase"><i class="fas fa-chart-bar me-2 text-danger"></i> Returns by Department</h6>
                    <small class="text-muted">Shows which departments receive the highest volume of misrouted AI tickets</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($misclassificationsByDept ?? [] as $metric)
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <div class="small text-muted fw-bold text-truncate">{{ $metric->department->dep_name ?? 'Unknown Dept' }}</div>
                                    <div class="fs-4 fw-bold text-dark mt-1">{{ $metric->count }} <span class="small font-weight-normal text-muted font-size-sm">returns</span></div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-3">
                                <small>No returned ticket data recorded yet. Great routing stability!</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-muted text-uppercase"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Misclassified Tickets Awaiting Reassignment</h6>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ count($misclassifiedTickets ?? []) }} Items</span>
        </div>
        <div class="card-body p-0"> <!-- Added p-0 to remove extra padding around the scrollbar -->
            <!-- Added inline style for max-height and scrolling -->
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top" style="z-index: 1;"> <!-- Added sticky-top -->
                        <tr>
                            <th class="ps-3 border-0">Ticket #</th>
                            <th class="border-0">Original Feedback</th>
                            <th class="border-0">Return Source & Reason</th>
                            <th class="border-0">Returned From Dept</th>
                            <th class="border-0 text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($misclassifiedTickets ?? [] as $return)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">#{{ $return->tck_id }}</td>
                                <td>
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $return->fbk_details }}">
                                        "{{ $return->fbk_details ?? 'No details' }}"
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $return->routing_source === 'AI Misclassification' ? 'bg-danger' : 'bg-warning text-dark' }} mb-1">
                                        {{ $return->routing_source }}
                                    </span>
                                    <br>
                                    <span class="text-muted small text-truncate d-inline-block" style="max-width: 200px;" title="{{ $return->return_reason }}">
                                        {{ $return->return_reason }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-block small fw-bold">{{ $return->dep_name ?? 'N/A' }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($return->returned_at)->format('M d, Y h:i A') }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('workflow.feedback_details', $return->fbk_id) }}" class="btn btn-sm btn-outline-dark px-3" target="_blank">
                                        <i class="fas fa-random me-1"></i> Re-route
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i> No returned tickets pending triage right now.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 fw-bold text-muted text-uppercase">
                    <i class="fas fa-sliders-h me-2 text-primary"></i> Confidence Score vs. Intervention Summary
                </h6>
                <small class="text-muted">Breakdown of AI predictions and resulting system actions</small>
            </div>
            <button data-bs-toggle="modal" data-bs-target="#exportReportModal" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-file-pdf me-1"></i> Export Report
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Confidence Range</th>
                            <th>Classification Standard</th>
                            <th>Volume</th>
                            <th>Action Captured</th>
                            <th>Intervention Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <tr>
                            <td class="fw-bold text-success">85.0% - 100.0%</td>
                            <td>High Precision</td>
                            <td class="fs-6 fw-bold">{{ $countHighPrecision ?? 0 }}</td>
                            <td>Auto-Routed</td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Bypassed (No Intervention)</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">{{ $currentThreshold }}% - 84.9%</td>
                            <td>Standard Confidence</td>
                            <td class="fs-6 fw-bold">{{ $countStandardConfidence ?? 0 }}</td>
                            <td>Auto-Routed</td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Bypassed (No Intervention)</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-danger">&lt; {{ $currentThreshold }}%</td>
                            <td>Low Confidence</td>
                            <td class="fs-6 fw-bold">{{ $countLowConfidence ?? 0 }}</td>
                            <td>Flagged for Triage</td>
                            <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle">Human Intervention Required</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">Any (&ge; {{ $currentThreshold }}%)</td>
                            <td>Restricted Category Override</td>
                            <td class="fs-6 fw-bold">{{ $countRestrictedOverride ?? 0 }}</td>
                            <td>Policy Override</td>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle">Mandatory Human Review</span></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Total Predictions Monitored:</td>
                            <td class="fs-6 text-primary">
                                {{ ($countHighPrecision ?? 0) + ($countStandardConfidence ?? 0) + ($countLowConfidence ?? 0) + ($countRestrictedOverride ?? 0) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Export Report Configuration Modal -->
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.reports.export') }}" method="GET" target="_blank">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="exportReportModalLabel">
                        <i class="fas fa-file-export me-2 text-primary"></i> Configure Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <!-- Report Type Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Report Type</label>
                        <select name="report_type" id="reportTypeSelect" class="form-select" required>
                            <option value="triage_audit">Misclassified Feedback Report</option>
                            <option value="intervention_summary">AI Confidence & Intervention Summary</option>
                            <!-- <option value="raw_predictions">Raw Prediction Data Log</option> -->
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted small text-uppercase">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted small text-uppercase">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Dynamic Filter Option 2: Routing Source Filter (For Misclassified Feedback Report) -->
                    <div class="mb-4" id="routingSourceFilterWrapper">
                        <label class="form-label fw-bold text-muted small text-uppercase">Misclassification Source</label>
                        <select name="routing_source_filter" class="form-select">
                            <option value="all">All Misclassifications (AI & Human)</option>
                            <option value="ai_only">Only AI Misclassifications</option>
                            <option value="admin_only">Only Staff / Human Misclassifications</option>
                        </select>
                    </div>

                    <!-- Dynamic Filter Option 1: Prediction Filter (For AI Summary & Raw Predictions) -->
                    <div class="mb-4 d-none" id="predictionFilterWrapper">
                        <label class="form-label fw-bold text-muted small text-uppercase">Intervention Filter</label>
                        <select name="intervention_filter" class="form-select">
                            <option value="all">Include All Predictions</option>
                            <option value="auto_only">Only Auto-Routed (Successful)</option>
                            <option value="manual_only">Only Human Interventions (Triaged)</option>
                        </select>
                    </div>

                    <!-- Export Format -->
                    <div>
                        <label class="form-label fw-bold text-muted small text-uppercase d-block">Export Format</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf" checked>
                            <label class="form-check-label" for="formatPdf"><i class="fas fa-file-pdf text-danger me-1"></i> PDF Document</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="formatCsv" value="csv">
                            <label class="form-check-label" for="formatCsv"><i class="fas fa-file-csv text-success me-1"></i> CSV / Excel</label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-download me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportTypeSelect = document.getElementById('reportTypeSelect');
        const predictionFilterWrapper = document.getElementById('predictionFilterWrapper');
        const routingSourceFilterWrapper = document.getElementById('routingSourceFilterWrapper');

        reportTypeSelect.addEventListener('change', function () {
            if (this.value === 'triage_audit') {
                predictionFilterWrapper.classList.add('d-none');
                routingSourceFilterWrapper.classList.remove('d-none');
            } else {
                predictionFilterWrapper.classList.remove('d-none');
                routingSourceFilterWrapper.classList.add('d-none');
            }
        });
    });
</script>