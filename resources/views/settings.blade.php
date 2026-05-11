@extends('layouts.app')

@section('title', 'Admin Settings')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-maroon"><i class="fas fa-cog me-2"></i> Settings</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <strong class="text-secondary"><i class="fas fa-user-circle me-1"></i> Account Settings</strong>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success small py-2">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="adminName" class="form-label small fw-bold text-secondary">Full Name</label>
                            <input type="text" name="usr_name" class="form-control" id="adminName" value="{{ auth()->user()->usr_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="adminMobile" class="form-label small fw-bold text-secondary">Mobile Number</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted fw-bold">+63</span>
                                <input 
                                    type="text" 
                                    name="usr_mobile" 
                                    class="form-control" 
                                    id="adminMobile" 
                                    maxlength="10"
                                    minlength="10"
                                    pattern="[0-9]{10}"
                                    value="{{ auth()->user()->usr_mobile }}" 
                                    maxlength="10"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    placeholder="9123456789"
                                >
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="adminCode" class="form-label small fw-bold text-secondary">User Code</label>
                            <input disabled type="text" class="form-control bg-light" id="adminCode" value="{{ auth()->user()->usr_code }}" readonly>
                            <small class="text-muted">Your user code is used for logging in and cannot be changed.</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 fw-bold text-dark">Change Password</h6>
                        <p class="small text-muted mb-3">Leave these fields blank if you do not wish to change your password.</p>

                        <div class="mb-3">
                            <label for="currentPassword" class="form-label small fw-bold text-secondary">Current Password</label>
                            <input type="password" name="current_password" class="form-control" id="currentPassword">
                        </div>

                        <div class="mb-3">
                            <label for="adminPassword" class="form-label small fw-bold text-secondary">New Password</label>
                            <input type="password" name="new_password" class="form-control" id="adminPassword">
                        </div>

                        <div class="mb-4">
                            <label for="adminPasswordConfirm" class="form-label small fw-bold text-secondary">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" id="adminPasswordConfirm">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-maroon px-4 shadow-sm">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <strong class="text-secondary"><i class="fas fa-id-badge me-1"></i> Assignment Details</strong>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Branch</label>
                        <div class="p-3 bg-light border rounded d-flex align-items-center">
                            <i class="fas fa-building fs-4 text-maroon me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ auth()->user()->branch_id ?? 'No Branch Assigned' }}</h6>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-2">Assigned Departments</label>
                        <div class="p-3 bg-light border rounded">
                            @if(session()->has('user_department_names') && count(session('user_department_names')) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(session('user_department_names') as $id => $name)
                                        <span class="badge bg-maroon fw-normal fs-6 shadow-sm"><i class="fas fa-tag me-1"></i> {{ $name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="small text-muted mb-0 fst-italic">No departments currently assigned.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection