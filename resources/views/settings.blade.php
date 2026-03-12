@extends('layouts.app')

@section('title', 'Admin Settings')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">⚙️ Settings</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong>👤 Account Settings</strong>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="adminName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="adminName" placeholder="Juan Admin">
                        </div>

                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="adminEmail" placeholder="admin@example.com">
                        </div>

                        <div class="mb-3">
                            <label for="adminPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="adminPassword">
                        </div>

                        <div class="mb-3">
                            <label for="adminPasswordConfirm" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="adminPasswordConfirm">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong>⚙️ System Preferences</strong>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="autoRouting" checked>
                            <label class="form-check-label" for="autoRouting">Enable Auto-Routing</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="notifyNewFeedback">
                            <label class="form-check-label" for="notifyNewFeedback">Email for New Feedback</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="lowConfidenceFlag" checked>
                            <label class="form-check-label" for="lowConfidenceFlag">Highlight Low-Confidence Feedback</label>
                        </div>

                        <button type="submit" class="btn btn-secondary">Update Preferences</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
