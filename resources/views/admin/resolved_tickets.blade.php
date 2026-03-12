@extends('layouts.app')

@section('title', 'Resolved Tickets History')
@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center border-bottom pb-3">
        <h2 class="h4 text-gray-800 mb-0">
            <i class="fas fa-history text-primary me-2"></i>Resolved Tickets History
        </h2>
        <div>
            <button class="btn btn-outline-success btn-sm shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export Report
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form action="{{ route('admin.resolved_tickets') }}" method="GET">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Ticket #, Name, or Keywords..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Department</label>
                        <select name="dep_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dep)
                                <option value="{{ $dep->dep_id }}" {{ request('dep_id') == $dep->dep_id ? 'selected' : '' }}>
                                    {{ $dep->dep_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}" {{ request('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                    {{ $branch->branch_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Date Resolved (From)</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-md-3 offset-md-9 d-flex gap-2">
                        <a href="{{ route('admin.resolved_tickets') }}" class="btn btn-light border w-50">
                            <i class="fas fa-redo me-1"></i> Clear
                        </a>
                        <button type="submit" class="btn btn-maroon w-50 shadow-sm">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 15%;">Ticket & Date</th>
                            <th style="width: 20%;">Sender Info</th>
                            <th style="width: 30%;">Feedback Details</th>
                            <th style="width: 35%;">Resolution & Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-maroon">#{{ $t->tck_id }}</div>
                                <div class="small text-muted mt-1">
                                    <i class="fas fa-check-circle text-success me-1"></i> Resolved On:<br>
                                    {{ $t->tck_date_action ? \Carbon\Carbon::parse($t->tck_date_action)->format('M d, Y h:i A') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $t->feedback->std_name ?? 'Anonymous' }}</div>
                                <div class="small text-muted">{{ $t->feedback->std_id_no ?? 'No ID' }}</div>
                                <span class="badge bg-light text-dark border mt-1">{{ $t->feedback->branch_id }}</span>
                                <div class="small text-muted mt-1">{{ $t->feedback->type->typ_value ?? '' }}</div>
                            </td>
                            <td>
                                <p class="mb-0 small text-wrap pe-2" style="max-height: 80px; overflow-y: auto; font-style: italic;">
                                    "{{ $t->feedback->fbk_details }}"
                                </p>
                            </td>
                            <td class="pe-4">
                                <div class="badge bg-maroon text-white mb-2">{{ $t->department->dep_name ?? 'Unknown Dept' }}</div>
                                <div class="p-2 bg-light border rounded small text-wrap" style="max-height: 80px; overflow-y: auto;">
                                    <strong>Action Taken:</strong> {{ $t->tck_action_details ?? 'No details provided.' }}
                                </div>
                                <div class="small text-muted mt-1 text-end">
                                    Resolved by: {{ $t->actionBy->name ?? 'System' }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block text-light"></i>
                                No resolved tickets match your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($tickets->hasPages())
                <div class="card-footer bg-white pt-3 border-top-0">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .text-maroon { color: maroon; }
    .bg-maroon { background-color: maroon; }
    .btn-maroon { background-color: maroon; color: white; border: none; }
    .btn-maroon:hover { background-color: #600000; color: white; }
</style>
@endsection