@extends('layouts.app')

@section('title', 'Feedback Transactions')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-maroon"><i class="fas fa-list-alt me-2"></i> Feedback Transactions</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4">Ticket ID</th>
                            <th>Submitter</th>
                            <th>Type / Theme</th>
                            <th>Department</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">#{{ $ticket->tck_id }}</td>
                                <td>
                                    {{ $ticket->feedback->std_name ?? 'Guest' }}
                                    <br><small class="text-muted">{{ $ticket->feedback->std_id_no ?? 'No ID' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $ticket->feedback->type->typ_name ?? 'N/A' }}</span>
                                    <br><small class="text-muted">{{ $ticket->feedback->theme->thm_name ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $ticket->department->dep_name ?? 'Unassigned' }}</td>
                                <td>{{ \Carbon\Carbon::parse($ticket->tck_date_created)->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($ticket->tck_date_verified)
                                        <span class="badge bg-success">Resolved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Open / In Progress</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer bg-white pt-3 border-top-0">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection