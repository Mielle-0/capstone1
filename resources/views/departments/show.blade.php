@extends('layouts.app')

@section('title', 'Tickets - ' . $department->dep_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Feedback for Action</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold">{{ $department->dep_name }}</h2>
        </div>
        <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
            <i class="fas fa-sync"></i> Refresh Data
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Ref #</th>
                            <th>Feedback Details</th>
                            <th>Date Received</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4 fw-semibold text-primary">
                                #{{ $ticket->fbk_id }}
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;">
                                    {{ Str::limit($ticket->fbk_description, 60) }}
                                </div>
                                <small class="text-muted">By: {{ $ticket->creator->usr_name ?? 'System' }}</small>
                            </td>
                            <td>
                                {{ $ticket->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if(is_null($ticket->tck_action_by))
                                    <span class="badge rounded-pill bg-danger">Pending Action</span>
                                @elseif(is_null($ticket->tck_verified_by))
                                    <span class="badge rounded-pill bg-warning text-dark">Awaiting Verification</span>
                                @else
                                    <span class="badge rounded-pill bg-success">Completed</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="/tickets/{{ $ticket->fbk_id }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No feedback tickets found for this department.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())
        <div class="card-footer bg-white">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection