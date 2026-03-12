@extends('layouts.app')

@section('title', 'Feedback Validation')
@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form action="{{ route('workflow.validation') }}" method="GET">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, ID, or Keywords..." value="{{ request('search') }}">
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
                        <label class="form-label small fw-bold text-muted">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-6 text-end">
                        <a href="{{ route('workflow.validation') }}" class="btn btn-light border me-2">
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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 25%;">Sender & Timeline</th>
                            <th style="width: 35%;">Feedback Details</th>
                            <th style="width: 15%;">Classification</th>
                            <th class="text-center" style="width: 25%;">Routing Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $fb)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-maroon">{{ $fb->std_name }}</div>
                                <div class="small text-muted">{{ $fb->std_id_no ?? 'No ID' }}</div>
                                <div class="small fw-bold text-secondary mt-2">
                                    <i class="far fa-calendar-alt me-1"></i> 
                                    {{ $fb->fbk_date_created ? $fb->fbk_date_created->format('M d, Y h:i A') : 'N/A' }}
                                </div>
                                <span class="badge bg-light text-dark border mt-1">{{ $fb->branch_id }}</span>
                            </td>
                            <td>
                                <p class="mb-0 small text-wrap pe-2" style="max-height: 100px; overflow-y: auto;">
                                    "{{ $fb->fbk_details }}"
                                </p>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $fb->type->typ_value ?? 'N/A' }}</div>
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">
                                    {{ $fb->theme->thm_value ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="pe-4">
                                @php
                                    // Fetch specific data for THIS row
                                    $prediction = $fb->prediction;
                                    $topGuess = $prediction ? $prediction->candidates->first() : null;
                                    
                                    // Use variables passed from Controller ($threshold, $aiEnabled)
                                    $isConfident = $topGuess && ($topGuess->probability >= $threshold);
                                    $shouldShowAi = $aiEnabled && $isConfident;

                                    // Prepare Tooltip once
                                    $tooltipData = $prediction 
                                        ? $prediction->candidates->map(fn($c) => "Rank #{$c->rank}: {$c->department->dep_name} (" . number_format($c->probability * 100, 0) . "%)")->implode("\n") 
                                        : "";
                                @endphp
                                
                                <form action="{{ route('workflow.process', $fb->fbk_id) }}" method="POST" class="row g-2 align-items-center">
                                    @csrf
                                    <div class="col-12 text-start">
                                        {{-- AI Prediction UI Element --}}
                                        @if($shouldShowAi)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-warning text-dark shadow-sm" style="font-size: 0.65rem;" 
                                                    data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $tooltipData }}">
                                                    <i class="fas fa-robot me-1"></i> AI SUGGESTED
                                                </span>
                                            </div>
                                        @endif

                                        <select name="dep_id" class="tom-select-dept" placeholder="Search department..." required>
                                            <option value="">-- Assign Dept --</option>

                                            @if($shouldShowAi)
                                                <optgroup label="✨ AI Recommendations">
                                                    @foreach($prediction->candidates as $candidate)
                                                        @if($candidate->probability >= $threshold)
                                                            <option value="{{ $candidate->dep_id }}" {{ $loop->first ? 'selected' : '' }}>
                                                                {{ $candidate->department->dep_name }} 
                                                                ({{ number_format($candidate->probability * 100, 0) }}% Match)
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </optgroup>
                                            @endif

                                            @foreach($departments as $branchId => $branchDepts)
                                                <optgroup label="Branch: {{ $branchId }}"> 
                                                    @foreach($branchDepts as $dep)
                                                        <option value="{{ $dep->dep_id }}">{{ $dep->dep_name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    {{-- Action Buttons --}}
                                    <div class="col-6 mt-2">
                                        <button name="action" value="approve" class="btn btn-maroon btn-sm w-100 shadow-sm"><i class="fas fa-ticket-alt"></i> Ticket</button>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <button name="action" value="reject" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Drop feedback?')"><i class="fas fa-trash"></i> Drop</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                Queue is completely clear!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($feedbacks->hasPages())
                <div class="card-footer bg-white pt-3 border-top-0">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .text-maroon { color: maroon; }
    .btn-maroon { background-color: maroon; color: white; border: none; }
    .btn-maroon:hover { background-color: #600000; color: white; }
    .border-maroon:focus { border-color: maroon; box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.1); }
    .border-warning:focus { border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); }

    /* Force Tom Select to use Bootstrap's font and styling */
    .ts-wrapper .ts-control, 
    .ts-wrapper .ts-dropdown {
        font-family: var(--bs-body-font-family) !important;
        font-size: 0.875rem !important; /* Matches form-control-sm */
    }

    .ts-wrapper.searchable-select .ts-control {
        border-color: #800000; /* Maroon border to match your theme */
    }

    /* Style the optgroup headers to be subtle but readable */
    .ts-dropdown .optgroup-header {
        font-weight: bold;
        color: #6c757d;
        background-color: #f8f9fa;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
</style>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize all Tom Selects at once using a class selector
        document.querySelectorAll('.tom-select-dept').forEach((el) => {
            new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                // This ensures the dropdown doesn't get cut off by table responsive containers
                dropdownParent: 'body' 
            });
        });

        // Bonus: Initialize your Bootstrap tooltips so the AI "Match %" shows up
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection