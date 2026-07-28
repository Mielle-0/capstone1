@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-4">
        <div>
            <h2 class="h4 fw-bold">User Management</h2>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-user-plus me-2"></i>Add User
        </button>
    </div>

     @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm auto-dismiss">{{ session('success') }}</div>
    @endif 

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm auto-dismiss">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
                {{-- Search Input (Reduced to 5 columns) --}}
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search name or code..." value="{{ request('search') }}">
                </div>
                
                {{-- New Branch Dropdown (3 columns) --}}
                <div class="col-md-3">
                    <select name="branch" class="form-select">
                        <option value="" {{ $branchFilter === null || $branchFilter === '' ? 'selected' : '' }}>
                            All Branches
                        </option>
                        
                        @foreach($branches as $branch)
                            <option value="{{ $branch->branch_id }}" {{ trim((string) $branchFilter) === trim((string) $branch->branch_id) ? 'selected' : '' }}>
                                {{ $branch->branch_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Role Dropdown (Reduced to 2 columns) --}}
                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->rol_id }}" {{ request('role') == $role->rol_id ? 'selected' : '' }}>
                                {{ $role->rol_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button (Stays at 2 columns) --}}
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
                        <th>Branch</th>
                        <th>Assigned Roles</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="{{ $user->usr_active ? '' : 'table-light opacity-75' }}" 
                        onclick='editUser(@json($user))' 
                        style="cursor: pointer;">
                        <td class="py-4 ps-4">
                            <div class="fw-bold">{{ $user->usr_name }}</div>
                        </td>
                        <td><code>{{ $user->usr_code }}</code></td>
                        <td><code>{{ $user->branch_id }}</code></td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-secondary-subtle text-secondary border">{{ $role->rol_name }}</span>
                            @endforeach
                        </td>
                        <td class="pe-4">
                            @if($user->usr_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
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

{{-- Unified Add/Edit User Modal --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="userForm" method="POST" class="modal-content">
            @csrf
            {{-- Dynamic Method Spoofing (Switches to PUT on Edit) --}}
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="userModalTitle">Register New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="usr_name" id="modal_usr_name" class="form-control" required placeholder="John Doe">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="usr_email" id="modal_usr_email" class="form-control" required placeholder="john@example.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">User Code</label>
                        <div class="input-group">
                            <input type="text" name="usr_code" id="modal_usr_code" class="form-control" required placeholder="1234" maxlength="4">
                            <button class="btn btn-outline-primary" type="button" id="btnGenerateCode" title="Generate Unique Code">
                                <i class="fas fa-random"></i> Generate
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" id="modal_branch_id" class="form-select" required>
                            <option value="">Select Branch...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Status is hidden during Add, takes up full width when shown --}}
                <div class="mb-3 d-none" id="statusSection">
                    <label class="form-label">Account Status</label>
                    <select name="usr_active" id="modal_usr_active" class="form-select w-50">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                {{-- Departments Grid --}}
                <div class="mb-3">
                    <label class="form-label">Assign Departments</label>
                    <select name="departments[]" id="modal_departments" multiple class="form-control" placeholder="Displaying departments by branch...">
                        </select>
                </div>

                {{-- Roles Grid --}}
                <div class="mb-3">
                    <label class="form-label">Assign Roles</label>
                    <div class="p-3 border rounded bg-light">
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $role->rol_id }}" id="role_{{ $role->rol_id }}">
                                        <label class="form-check-label text-truncate w-100" for="role_{{ $role->rol_id }}" title="{{ $role->rol_name }}">
                                            {{ $role->rol_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="modalSubmitBtn" class="btn btn-primary px-4">Save User</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Base URLs injected from Laravel
const storeUrl = "{{ route('admin.users.store') }}";
const updateUrlBase = "{{ url('admin/manage-users') }}"; 
const generateCodeUrl = "{{ route('admin.users.generate-code') }}";

const groupedDepartments = @json($departments);
let deptSelectInstance;

document.addEventListener('DOMContentLoaded', function() {

    setTimeout(function() {
        let alerts = document.querySelectorAll('.auto-dismiss');
        
        alerts.forEach(function(alert) {
            // Apply a smooth fade-out transition
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            
            // Wait for the fade out to finish (500ms), then remove from DOM completely
            setTimeout(() => alert.remove(), 500); 
        });
    }, 3000);
    window.userModal = new bootstrap.Modal(document.getElementById('userModal'));

    deptSelectInstance = new TomSelect('#modal_departments', {
        plugins: ['remove_button'], 
        create: false,
        sortField: { field: "text", direction: "asc" }
    });

    // 💡 Listen for changes on the Branch dropdown
    document.getElementById('modal_branch_id').addEventListener('change', function(e) {
        updateDepartmentOptions(e.target.value);
    });

    document.getElementById('btnGenerateCode').addEventListener('click', function() {
        const btn = this;
        const input = document.getElementById('modal_usr_code');
        
        // Temporarily disable button and show loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(generateCodeUrl)
            .then(response => response.json())
            .then(data => {
                input.value = data.code;
            })
            .catch(error => {
                console.error('Error generating code:', error);
                alert('Failed to generate code. Please try again.');
            })
            .finally(() => {
                // Restore button state
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-random"></i> Generate';
            });
    });
});

function updateDepartmentOptions(branchId, selectedValues = []) {
    deptSelectInstance.clear();        // Remove current selections
    deptSelectInstance.clearOptions(); // Remove current list of options
    
    if (branchId && groupedDepartments[branchId]) {
        // Add options for the selected branch
        groupedDepartments[branchId].forEach(dept => {
            deptSelectInstance.addOption({value: dept.dep_id, text: dept.dep_name});
        });
        
        // Update placeholder
        document.querySelector('#modal_departments-ts-control').setAttribute('placeholder', 'Search departments...');
    } else {
        document.querySelector('#modal_departments-ts-control').setAttribute('placeholder', 'Select a branch first...');
    }

    // If editing a user, re-select their existing departments
    if (selectedValues.length > 0) {
        deptSelectInstance.setValue(selectedValues);
    }
}

// 1. Function to open the modal for ADDING
function openAddModal() {
    document.getElementById('userModalTitle').innerText = 'Register New User';
    document.getElementById('userForm').action = storeUrl;
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalSubmitBtn').innerText = 'Create Account';

    // Unlock inputs for adding
    document.getElementById('modal_usr_name').readOnly = false;
    document.getElementById('modal_usr_code').readOnly = false;
    document.getElementById('modal_usr_email').readOnly = false;
    
    document.getElementById('modal_usr_name').disabled = false;
    document.getElementById('modal_usr_code').disabled = false;
    document.getElementById('modal_usr_email').disabled = false;
    document.getElementById('modal_branch_id').disabled = false;

    // Hide status section for new users
    document.getElementById('statusSection').classList.add('d-none');

    // Clear previous form data
    document.getElementById('userForm').reset();
    document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);

    // Clear Tom Select dropdown
    deptSelectInstance.clear();
    deptSelectInstance.clearOptions();
    document.querySelector('#modal_departments-ts-control').setAttribute('placeholder', 'Select a branch first...');
    
    document.getElementById('btnGenerateCode').disabled = false;
    document.getElementById('btnGenerateCode').classList.remove('d-none');


    window.userModal.show();
}

// 2. Function to open the modal for EDITING
function editUser(user) {
    
    document.getElementById('userModalTitle').innerText = 'Edit User';
    document.getElementById('userForm').action = `${updateUrlBase}/${user.usr_id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalSubmitBtn').innerText = 'Save Changes';
    
    document.getElementById('modal_branch_id').value = user.branch_id;

    // Lock inputs so they cannot be changed
    document.getElementById('modal_usr_name').readOnly = true;
    document.getElementById('modal_usr_code').readOnly = true;
    document.getElementById('modal_usr_email').readOnly = true;
    
    document.getElementById('modal_usr_name').disabled = true;
    document.getElementById('modal_usr_code').disabled = true;
    document.getElementById('modal_usr_email').disabled = true;
    document.getElementById('modal_branch_id').disabled = true; 

    // Show the status toggle
    document.getElementById('statusSection').classList.remove('d-none');
    document.getElementById('modal_usr_active').value = user.usr_active;

    // Populate standard inputs
    document.getElementById('modal_usr_name').value = user.usr_name;
    document.getElementById('modal_usr_code').value = user.usr_code;
    document.getElementById('modal_usr_email').value = user.usr_email;
    document.getElementById('modal_branch_id').value = user.branch_id;

    // Check appropriate role checkboxes
    document.querySelectorAll('.role-checkbox').forEach(cb => {
        cb.checked = user.roles ? user.roles.some(role => role.rol_id == cb.value) : false;
    });

    let userDeptIds = [];
    if (user.departments) {
        userDeptIds = user.departments.map(d => d.dep_id.toString());
    }
    
    // Populate dropdown options and set the selected values
    updateDepartmentOptions(user.branch_id, userDeptIds);

    document.getElementById('btnGenerateCode').disabled = true;
    document.getElementById('btnGenerateCode').classList.add('d-none');

    window.userModal.show();
}
</script>
@endpush