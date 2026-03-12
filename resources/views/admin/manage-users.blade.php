@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold">User Management</h2>
            <p class="text-muted small">Create and manage accounts for Encoders, Validators, and Department Heads.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-2"></i>Create User
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control" placeholder="Search name or code..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->rol_id }}" {{ request('role') == $role->rol_id ? 'selected' : '' }}>{{ $role->rol_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User Details</th>
                        <th>User Code</th>
                        <th>Assigned Roles</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="{{ $user->usr_active ? '' : 'table-light opacity-75' }}">
                        <td class="ps-4">
                            <div class="fw-bold">{{ $user->usr_name }}</div>
                            <small class="text-muted">ID: {{ $user->usr_id }}</small>
                        </td>
                        <td><code>{{ $user->usr_code }}</code></td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-secondary-subtle text-secondary border">{{ $role->rol_name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->usr_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <form action="{{ route('admin.users.toggle', $user->usr_id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $user->usr_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $user->usr_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" 
                                    onclick="editUser({{ json_encode($user) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Register New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="usr_name" class="form-control" required placeholder="John Doe">
                </div>
                <div class="mb-3">
                    <label class="form-label">User Code (Username)</label>
                    <input type="text" name="usr_code" class="form-control" required placeholder="EMP-1234">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Assign Roles</label>
                    <div class="p-3 border rounded bg-light">
                        @foreach($roles as $role)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->rol_id }}" id="role_{{ $role->rol_id }}">
                                <label class="form-check-label" for="role_{{ $role->rol_id }}">
                                    {{ $role->rol_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Create Account</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit user Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editUserForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="usr_name" id="edit_usr_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">User Code</label>
                    <input type="text" name="usr_code" id="edit_usr_code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $role->rol_id }}" id="edit_role_{{ $role->rol_id }}">
                            <label class="form-check-label">{{ $role->rol_name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>


@include('admin._scripts')
@endsection