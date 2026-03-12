@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">📊 Admin Dashboard</h3>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Feedback</h5>
                    <p class="display-6">124</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending Reviews</h5>
                    <p class="display-6">18</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Routed Today</h5>
                    <p class="display-6">32</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Unclassified</h5>
                    <p class="display-6">7</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder Charts --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Feedback Volume (Last 7 Days)</div>
                <div class="card-body">
                    {!! $feedbackChart->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Top Departments (This Week)</div>
                <div class="card-body">
                    {!! $topDepartmentsChart->container() !!}
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Chart Scripts --}}
<script src="{{ $feedbackChart->cdn() }}"></script>
{!! $feedbackChart->script() !!}
{!! $topDepartmentsChart->script() !!}
@endsection
