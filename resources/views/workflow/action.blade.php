@extends('layouts.app')

@section('title', 'Department Action Needed')
@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filter Card (Kept your existing filter logic) --}}
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form action="{{ route('workflow.action') }}" method="GET">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Ticket #, Name, ID, or Keywords..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Feedback Type</label>
                        <select name="typ_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type->typ_id }}" {{ request('typ_id') == $type->typ_id ? 'selected' : '' }}>
                                    {{ $type->typ_value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Ticket Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Ticket Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('workflow.action') }}" class="btn btn-light border me-2">
                            <i class="fas fa-redo me-1"></i> Clear
                        </a>
                        <button type="submit" class="btn btn-maroon px-4">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($tickets as $t)
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                        <h6 class="m-0 font-weight-bold">
                            {{-- 1. Made the title clickable --}}
                            <a href="{{ route('workflow.timeline', $t->feedback->fbk_id) }}" class="text-maroon text-decoration-none" target="_blank">
                                <i class="fas fa-ticket-alt me-2"></i>Ticket #{{ $t->tck_id }}
                            </a>
                        </h6>
                        <div>
                            {{-- 2. Updated the route and icon for the history button --}}
                            <a href="{{ route('workflow.timeline', $t->feedback->fbk_id) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-stream me-1"></i> View Timeline
                            </a>
                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending Action</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Left Column: Ticket Info --}}
                            <div class="col-md-4 border-end pe-4">
                                <div class="mb-3">
                                    <span class="small text-muted text-uppercase fw-bold">Sender Details</span><br>
                                    <strong>{{ $t->feedback->std_name ?? 'Anonymous' }}</strong> 
                                    ({{ $t->feedback->std_id_no ?? 'No ID' }})<br>
                                    <span class="badge bg-secondary mt-1"><i class="fas fa-map-marker-alt me-1"></i> {{ $t->feedback->branch_id }}</span>
                                    <span class="badge bg-info mt-1"><i class="fas fa-building me-1"></i> {{ $t->department->dep_name ?? 'No Dept' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="small text-muted text-uppercase fw-bold">Classification</span><br>
                                    <span class="fw-bold">{{ $t->feedback->type->typ_value ?? 'N/A' }}</span><br>
                                    <span class="small text-muted">{{ $t->feedback->theme->thm_value ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="small text-muted text-uppercase fw-bold">Reported On</span><br>
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    {{ $t->feedback->fbk_date_created ? $t->feedback->fbk_date_created->format('M d, Y h:i A') : 'N/A' }}
                                </div>
                            </div>

                            {{-- Right Column: Feedback, History & Action Form --}}
                            <div class="col-md-8 ps-4">
                                {{-- 1. The Feedback Message --}}
                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold d-block mb-1">Feedback Message</label>
                                    <div class="p-3 bg-light rounded" style="font-style: italic; border-left: 4px solid #ddd;">
                                        "{{ $t->feedback->fbk_details }}"
                                    </div>
                                </div>

                                {{-- 2. NEW: Action History (Latest first) --}}
                                @if($t->actions->count() > 0)
                                    <div class="mb-4">
                                        <label class="small text-muted text-uppercase fw-bold d-block mb-2">Previous Actions / Rejections</label>
                                        @foreach($t->actions()->latest('act_date_created')->take(2)->get() as $action)
                                            <div class="alert {{ $action->act_status == 2 ? 'alert-danger' : 'alert-light border' }} py-2 px-3 mb-2 small">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $action->creator->usr_name ?? 'Staff' }}</strong>
                                                    <span>{{ $action->act_date_created->format('M d, Y') }}</span>
                                                </div>
                                                <div class="text-dark my-1">{{ $action->act_details }}</div>
                                                
                                                @if($action->act_status == 2)
                                                    <div class="mt-1 text-danger font-weight-bold">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> 
                                                        Rejected: {{ $action->act_reject_details }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- 3. Action Form --}}
                                <form action="{{ route('workflow.submit_action', $t->tck_id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-3 border rounded shadow-sm">
                                    @csrf
                                    <label class="small text-muted text-uppercase fw-bold d-block mb-1">Action Taken <span class="text-danger">*</span></label>
                                    <textarea name="details" class="form-control mb-3 border-maroon focus-maroon" rows="3" placeholder="Describe the resolution or action taken by the department..." required></textarea>
                                    
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Evidence / Attachment (Optional)</label>
                                            <input type="file" name="act_file" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-5 text-end pt-3">
                                            <button type="submit" class="btn btn-maroon shadow-sm px-4" onclick="return confirm('Submit this action for verification?')">
                                                <i class="fas fa-paper-plane me-1"></i> Submit Action
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State (Kept your existing empty logic) --}}
            <div class="col-12">
                <div class="card shadow-sm border-0 py-5 text-center">
                    <div class="card-body text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                        <h5>No Pending Actions</h5>
                        <p class="mb-0">All assigned tickets have been resolved or do not match your current filters.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($tickets->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $tickets->links() }}
        </div>
    @endif
</div>

<style>
    .text-maroon { color: maroon; }
    .btn-maroon { background-color: maroon; color: white; border: none; }
    .btn-maroon:hover { background-color: #600000; color: white; }
    .border-maroon:focus, .focus-maroon:focus { border-color: maroon; box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.1); }
    .border-maroon { border-color: #dee2e6; }
</style>
@endsection