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
                        <a  class="btn btn-light border me-2">
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


        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>{{ session('warning') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

        </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 15%;">Sender & Timeline</th>
                            <th style="width: 35%;">Feedback Details</th>
                            <th style="width: 15%;">Classification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $fb)
                        <tr class="clickable-row position-relative" 
                            data-href="{{ route('workflow.feedback_details', $fb->fbk_id) }}" 
                            style="cursor: pointer; transition: background-color 0.2s;">

                            <td class="ps-4 align-top pt-3">
                                <div class="fw-bold text-maroon">{{ $fb->std_name }}</div>
                                <div class="small text-muted">{{ $fb->std_id_no ?? 'No ID' }}</div>
                                <div class="small fw-bold text-secondary mt-1">
                                    {{ $fb->fbk_date_created ? $fb->fbk_date_created->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>
                            
                            <td class="align-top pt-3">
                                <p class="mb-1 small text-dark">
                                    "{{ Str::limit($fb->fbk_details, 85, '...') }}"
                                </p>
                            </td>
                            
                            <td class="align-top pt-3">
                                <div class="small fw-bold">{{ $fb->type->typ_value ?? 'N/A' }}</div>
                                <span class="badge bg-light text-dark border mt-1">{{ $fb->branch_id }}</span>
                            </td>
                        </tr>
                        @empty
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

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        const rows = document.querySelectorAll('.clickable-row');
        
        rows.forEach(row => {
            // Add a slight hover effect
            row.addEventListener('mouseenter', () => row.classList.add('bg-light'));
            row.addEventListener('mouseleave', () => row.classList.remove('bg-light'));

            // Handle the click
            row.addEventListener('click', function(e) {
                // If the user is interacting with a form, button, link, or dropdown, do nothing!
                if (e.target.closest('button, a, select, form, .ts-control, .ts-dropdown')) {
                    return; 
                }
                
                // Otherwise, navigate to the details view
                window.location.href = this.dataset.href;
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