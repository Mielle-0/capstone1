@extends('layouts.app')

@section('title', 'Manage Departments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold">Department Management</h2>
            <p class="text-muted small">Manage organizational units and assigned personnel.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="fas fa-plus me-2"></i>Add Department
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('admin.departments.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Dept Name</th>
                            <th>Full Description</th>
                            <th>Assigned Staff</th>
                            <th class="text-center">Active Tickets</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $dept)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">{{ $dept->dep_name }}</span>
                            </td>
                            <td>{{ $dept->dep_full_name }}</td>
                            <td>
                                @if($dept->users->count() > 0)
                                    <div class="avatar-group">
                                        @foreach($dept->users->take(3) as $user)
                                            <span class="badge bg-info text-dark" title="{{ $user->usr_name }}">
                                                {{ Str::limit($user->usr_name, 10) }}
                                            </span>
                                        @endforeach
                                        @if($dept->users->count() > 3)
                                            <small class="text-muted">+{{ $dept->users->count() - 3 }} more</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border">
                                    {{ $dept->tickets_count }}
                                </span>
                            </td>
                            <td>
                                @if($dept->dep_active)
                                    <span class="text-success"><i class="fas fa-circle small me-1"></i> Active</span>
                                @else
                                    <span class="text-danger"><i class="fas fa-circle small me-1"></i> Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" 
                                        onclick="editDepartment({{ json_encode($dept) }})">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-primary" title="Assign" 
                                        onclick="openAssignModal('{{ $dept->dep_id }}', '{{ $dept->dep_name }}')">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $departments->links() }}
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editDeptForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Short Name</label>
                    <input type="text" name="dep_name" id="edit_dep_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <input type="text" name="dep_full_name" id="edit_dep_full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="dep_active" id="edit_dep_active" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Assign Modal --}}
<div class="modal fade" id="assignUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.departments.assign') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Assign Staff to <span id="displayDeptName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="dep_id" id="inputDeptId">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Staff Member</label>
                    <select name="usr_id" class="form-select" required>
                        <option value="">-- Choose User --</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->usr_id }}">{{ $user->usr_name }} ({{ $user->usr_code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Confirm Assignment</button>
            </div>
        </form>
    </div>
</div>
@include('admin._scripts')
@endsection