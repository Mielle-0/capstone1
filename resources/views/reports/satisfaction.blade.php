@extends('layouts.app')

@section('title', 'Customer Satisfaction')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-maroon"><i class="fas fa-smile me-2"></i> Customer Satisfaction (CSAT)</h2>

    <div class="row mb-4">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card shadow-sm border-0 h-100 text-center py-4">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-secondary mb-3">System-Wide Average</h6>
                    <h1 class="display-3 fw-bold text-dark mb-2">{{ number_format($overallAverage, 1) }}</h1>
                    <div class="text-warning fs-3 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($overallAverage) ? '' : 'text-light' }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted">Out of 5 Stars</small>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card shadow-sm border-0 h-100 text-center py-4">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-secondary mb-3">Resolution Rate</h6>
                    <h1 class="display-3 fw-bold text-success mb-2">{{ $resolutionRate }}%</h1>
                    <p class="text-muted small">Tickets successfully closed and rated.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-secondary mb-3">Rating Breakdown</h6>
                    @php $totalRatings = array_sum($ratingDistribution->toArray()) ?: 1; @endphp
                    
                    @for($stars = 5; $stars >= 1; $stars--)
                        @php 
                            $count = $ratingDistribution[$stars] ?? 0;
                            $pct = ($count / $totalRatings) * 100;
                        @endphp
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2 text-muted fw-bold" style="width: 45px;">{{ $stars }} <i class="fas fa-star text-warning small"></i></span>
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-maroon" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="ms-2 text-muted small" style="width: 30px; text-align: right;">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom fw-bold text-secondary">Department Leaderboard</div>
                <ul class="list-group list-group-flush">
                    @foreach($departmentPerformance as $dept)
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="fw-bold text-dark">{{ $dept->dep_name }}</span>
                            <div>
                                <span class="fw-bold me-1">{{ number_format($dept->average_rating, 1) }}</span>
                                <i class="fas fa-star text-warning small"></i>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom fw-bold text-secondary">Recent Ratings</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentReviews as $review)
                            <div class="list-group-item p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="text-dark">Ticket #{{ $review->tck_id }}</strong>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->tck_rate ? '' : 'text-light' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-muted mb-2 small">{{ $review->feedback->fbk_details ?? 'No additional details provided.' }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="badge bg-light text-dark border">{{ $review->department->dep_name ?? 'Unassigned' }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($review->tck_date_verified)->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">No rated tickets available yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection