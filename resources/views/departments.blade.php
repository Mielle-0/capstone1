@php
$departments = [
    ['name' => 'Registrar', 'handled' => ['Complaint', 'Inquiry'], 'routed_count' => 12],
    ['name' => 'Finance', 'handled' => ['Complaint', 'Suggestion'], 'routed_count' => 9],
    ['name' => 'Library', 'handled' => ['Suggestion', 'Praise'], 'routed_count' => 6],
    ['name' => 'IT Services', 'handled' => ['Complaint'], 'routed_count' => 4],
];
@endphp

@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🏢 Departments</h2>
        <button class="btn btn-primary">➕ Add Department</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Department Name</th>
                        <th>Handles</th>
                        <th>Feedback Routed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $dept)
                    <tr>
                        <td><strong>{{ $dept['name'] }}</strong></td>
                        <td>
                            @foreach ($dept['handled'] as $type)
                                <span class="badge bg-secondary">{{ $type }}</span>
                            @endforeach
                        </td>
                        <td>{{ $dept['routed_count'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
