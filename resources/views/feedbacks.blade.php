@extends('layouts.app')

@section('title', 'All Feedback')

@section('content')

<?php $departments = [
    'College of Business Administration Education',
    'College of Arts and Sciences Education',
    'College of Teachers Education',
    'College of Engineering Education',
    'College of Legal Education',
    'Professional Schools',
    'College of Hospitality Education',
    'College of Computing Education',
    'College of Criminal Justice Education',
    'College of Accounting Education',
    'College of Architecture and Fine Arts Education',
    'College of Health Sciences Education',
    'Guidance Services and Testing Center',
    'Center for Health Services'
];

// Read the JSON file
$json = file_get_contents('sample_feedback.json');

// Decode JSON into a PHP array
$feedbacks = json_decode($json, true);
?>
<div class="container-fluid">
    <h2 class="mb-4">📂 All Feedback</h2>

    <!-- Filter Bar -->
    <form method="GET" class="row gy-2 gx-3 align-items-center mb-4 bg-light p-3 rounded">
        <div class="col-md-3">
            <label class="form-label">Classification</label>
            <select name="classification" class="form-select">
                <option value="">All</option>
                @foreach (['Complaint', 'Suggestion', 'Praise', 'Inquiry'] as $type)
                    <option value="{{ $type }}" {{ request('classification') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Department</label>
            <select name="department" class="form-select">
                <option value="">All</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Sort By</label>
            <select name="sort" class="form-select">
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date</option>
                <option value="confidence_score" {{ request('sort') == 'confidence_score' ? 'selected' : '' }}>Confidence</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Order</label>
            <select name="order" class="form-select">
                <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Feedback Table -->
<div class="table-responsive bg-white shadow-sm rounded">
    <table class="table table-hover align-middle table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Classification</th>
                <th>Predicted Classification</th>
                <th>Classification Confidence</th>
                <th>Department</th>
                <th>Predicted Department</th>
                <th>Department Confidence</th>
                <th>Submitted</th>
                <th>Validated</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback['id'] }}</td>
                    <td>{{ $feedback['name'] ?? 'Anonymous' }}</td>
                    <td>{{ $feedback['email'] ?? '-' }}</td>
                    <td>{{ $feedback['phone_num'] ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($feedback['message'], 40) }}</td>
                    <td><span class="badge bg-info text-dark">{{ $feedback['classification'] ?? '-' }}</span></td>
                    <td>{{ $feedback['predicted_classification'] ?? '-' }}</td>
                    <td>
                        @if(!is_null($feedback['classification_confidence']))
                            <span class="badge bg-{{ $feedback['classification_confidence'] >= 0.9 ? 'success' : 'warning' }}">
                                {{ round($feedback['classification_confidence'] * 100) }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $feedback['department'] ?? '-' }}</td>
                    <td>{{ $feedback['predicted_department'] ?? '-' }}</td>
                    <td>
                        @if(!is_null($feedback['department_confidence']))
                            <span class="badge bg-{{ $feedback['department_confidence'] >= 0.9 ? 'success' : 'warning' }}">
                                {{ round($feedback['department_confidence'] * 100) }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ isset($feedback['date_submitted']) ? \Carbon\Carbon::parse($feedback['date_submitted'])->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ isset($feedback['date_validated']) ? \Carbon\Carbon::parse($feedback['date_validated'])->format('Y-m-d H:i') : '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $feedback['status'] ?? 'Pending' }}</span></td>
                    <td>
                        <a href="{{ url('/feedback/' . $feedback['id']) }}" class="btn btn-sm btn-primary">View</a>
                        @if(!is_null($feedback['department_confidence']))
                        <a href="{{ url('/feedback/' . $feedback['id'] . '/route') }}" class="btn btn-sm btn-warning">Edit</a>
                        @endif
                        <!-- Delete button commented out for now -->
                        {{-- <form action="{{ url('/feedback/' . $feedback['id']) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this feedback?')">
                                Delete
                            </button>
                        </form> --}}
                    </td>
                </tr>
            @empty
                <tr><td colspan="16" class="text-center">No feedback found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>


    <!-- Pagination -->
    <div class="mt-4">
        {{-- {{ $feedbacks->withQueryString()->links() }} --}}
    </div>
</div>
@endsection
