@extends('layouts.app')

@section('title', 'Transaction Analysis')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-maroon"><i class="fas fa-chart-line me-2"></i> Transaction Analysis</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 bg-maroon text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-clock fa-3x opacity-50 me-3"></i>
                    <div>
                        <h6 class="mb-0 text-uppercase fw-bold opacity-75">Avg. Resolution Time</h6>
                        <h2 class="mb-0 fw-bold">{{ round($avgResolutionHours ?? 0, 1) }} Hours</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-tasks fa-3x text-secondary opacity-25 me-3"></i>
                    <div class="w-100">
                        <h6 class="mb-2 text-uppercase fw-bold text-secondary small">Status Distribution</h6>
                        <div class="d-flex justify-content-between mb-1 small fw-bold">
                            <span class="text-success">Resolved: {{ $statusDistribution['Resolved'] }}</span>
                            <span class="text-warning text-dark">Open: {{ $statusDistribution['Open/In Progress'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            @php 
                                $total = array_sum($statusDistribution) ?: 1; 
                                $resPct = ($statusDistribution['Resolved'] / $total) * 100;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $resPct }}%"></div>
                            <div class="progress-bar bg-warning" style="width: {{ 100 - $resPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-bold text-secondary">
                    30-Day Volume Trend
                </div>
                <div class="card-body">
                    <canvas id="volumeChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom fw-bold text-secondary">Department Workload</div>
                <ul class="list-group list-group-flush">
                    @foreach($departmentWorkload as $dept)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $dept->dep_name }}
                            <span class="badge bg-maroon rounded-pill">{{ $dept->tickets_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom fw-bold text-secondary">Branch Volume</div>
                <ul class="list-group list-group-flush">
                    @foreach($branchVolume as $branch)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $branch->branch_id }}
                            <span class="badge bg-secondary rounded-pill">{{ $branch->total }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('volumeChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($volumeTrends->toArray())) !!},
                datasets: [{
                    label: 'New Tickets',
                    data: {!! json_encode(array_values($volumeTrends->toArray())) !!},
                    borderColor: '#be0002',
                    backgroundColor: 'rgba(190, 0, 2, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    });
</script>
@endpush