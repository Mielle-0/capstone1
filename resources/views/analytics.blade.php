@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">📈 Feedback Analytics</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Feedback</h5>
                    <p class="card-text fs-4">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Reviewed</h5>
                    <p class="card-text fs-4">{{ $stats['reviewed'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Routed</h5>
                    <p class="card-text fs-4">{{ $stats['routed'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Avg. Confidence</h5>
                    <p class="card-text fs-4">{{ round($stats['avg_confidence'] * 100) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-3 shadow">
                <h6>📅 Feedback Volume (Last 30 Days)</h6>
                {!! $volumeChart->container() !!}
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-3 shadow">
                <h6>📋 Feedback by Classification</h6>
                {!! $classificationChart->container() !!}
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 shadow">
                <h6>🏢 Feedback by Department</h6>
                {!! $departmentChart->container() !!}
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 shadow">
                <h6>❗ Low Confidence Trends</h6>
                {!! $confidenceChart->container() !!}
            </div>
        </div>
    </div>
</div>

<script src="{{ $volumeChart->cdn() }}"></script>
{!! $volumeChart->script() !!}
{!! $classificationChart->script() !!}
{!! $departmentChart->script() !!}
{!! $confidenceChart->script() !!}
@endsection
