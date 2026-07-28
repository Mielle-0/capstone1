@extends('layouts.guest')


@section('title', 'Change Password')

@section('nav-title')
    <i class="fas fa-bullhorn me-2"></i> Change Password
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <strong class="text-secondary">Welcome, {{ $user->usr_name }}!</strong>
                    </div>
                    
                    <div class="card-body p-4">
                        <p class="small text-muted mb-4">Please create a secure password to activate your account and log in.</p>

                        @if($errors->any())
                            <div class="alert alert-danger small py-2">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ request()->fullUrl() }}">
                            @csrf

                            <div class="mb-3">
                                <label for="password" class="form-label small fw-bold text-secondary">New Password</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label small fw-bold text-secondary">Confirm Password</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-check-circle"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-maroon w-100 shadow-sm py-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Set Password & Login
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection