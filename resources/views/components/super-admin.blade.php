<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h5 class="text-secondary fw-bold mb-1">User Management</h5>
            <span class="text-muted small">Overview of system access and roles</span>
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


    <div class="d-flex justify-content-between align-items-end mb-4 mt-5">
        <div>
            <h5 class="text-secondary fw-bold mb-1">AI Routing Performance</h5>
            <span class="text-muted small">Monitor machine learning accuracy and thresholds</span>
        </div>
        <a href="/admin/ai-settings" class="btn btn-sm btn-outline-dark px-3">Configure AI</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-dark text-white h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="text-uppercase text-white-50 fw-bold mb-2">Model Accuracy (All Time)</h6>
                    <div class="d-flex align-items-baseline gap-2">
                        <h1 class="mb-0 fw-bold">{{ $aiAccuracyRate ?? '0' }}%</h1>
                        <span class="badge {{ ($aiAccuracyRate ?? 0) >= 80 ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $totalVerifiedPredictions ?? 0 }} Verifications
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
                        <div class="col-4 border-end">
                            <small class="text-muted d-block text-uppercase mb-1">Model Version</small>
                            <span class="fs-5 fw-bold text-dark">{{ $currentAiVersion ?? 'v1.0' }}</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block text-uppercase mb-1">Auto-Route Threshold</small>
                            <span class="fs-5 fw-bold text-dark">{{ $autoRouteThreshold ?? '85' }}%</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block text-uppercase mb-1">Status</small>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h6 class="mb-0 fw-bold text-muted text-uppercase">Recent Predictions vs Actual</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 border-0">Ticket ID</th>
                            <th class="border-0">AI Prediction</th>
                            <th class="border-0">Confidence</th>
                            <th class="border-0">Verified Department</th>
                            <th class="border-0">Result</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($recentPredictions ?? [] as $prediction)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">#{{ $prediction->fbk_id }}</td>
                                <td>
                                    @if($prediction->topCandidate)
                                        {{ $prediction->topCandidate->department->dep_name ?? 'Unknown Dept' }}
                                    @else
                                        <span class="text-muted fst-italic">No prediction</span>
                                    @endif
                                </td>
                                <td>
                                    @if($prediction->topCandidate)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $prediction->topCandidate->probability * 100 }}%"></div>
                                            </div>
                                            <span class="small text-muted">{{ number_format($prediction->topCandidate->probability * 100, 1) }}%</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-medium">
                                    {{ $prediction->verifiedDepartment->dep_name ?? 'Pending...' }}
                                </td>
                                <td>
                                    @if(!$prediction->verified_dept_id)
                                        <span class="badge bg-light text-dark border">Pending</span>
                                    @elseif($prediction->wasAiCorrect())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Correct</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Incorrect</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No recent AI predictions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>