@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4>⚡ For Action (Tickets)</h4>
    <p class="text-muted">Tickets assigned to your department requiring resolution.</p>

    <div class="row g-3">
        @foreach($tickets as $ticket)
        <div class="col-md-6">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="text-primary">#{{ $ticket->tck_uuid }}</h6>
                        <small class="text-muted">{{ $ticket->tck_date_created->diffForHumans() }}</small>
                    </div>
                    <p class="small mb-2"><strong>From:</strong> {{ $ticket->feedback->std_name }}</p>
                    <p class="mb-3">{{ $ticket->feedback->fbk_details }}</p>
                    
                    <form action="{{ route('ticket.resolve', $ticket->tck_id) }}" method="POST">
                        @csrf
                        <textarea name="tck_action_details" class="form-control form-control-sm mb-2" placeholder="Describe the action taken..."></textarea>
                        <button type="submit" class="btn btn-sm btn-warning">Mark as Resolved</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection