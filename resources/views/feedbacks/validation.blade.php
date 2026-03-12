@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>✓ For Validation</h4>
        <span class="badge bg-danger rounded-pill">{{ count($feedbacks) }} Pending</span>
    </div>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Date</th>
                    <th>Source</th>
                    <th>Category</th>
                    <th>Student</th>
                    <th>Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feedbacks as $fb)
                <tr>
                    <td>{{ $fb->fbk_date_created->format('M d, Y') }}</td>
                    <td>{{ $fb->type->typ_value }}</td>
                    <td>{{ $fb->theme->thm_value }}</td>
                    <td>{{ $fb->std_name }}</td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $fb->fbk_details }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#validateModal{{ $fb->fbk_id }}">Review</button>
                    </td>
                </tr>

                <div class="modal fade" id="validateModal{{ $fb->fbk_id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('feedback.validate', $fb->fbk_id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header"><h5>Validate Feedback</h5></div>
                                <div class="modal-body">
                                    <p><strong>Feedback:</strong> {{ $fb->fbk_details }}</p>
                                    <hr>
                                    <label class="form-label">Assign to Department</label>
                                    <select name="dep_id" class="form-select" required>
                                        @foreach($departments as $dep)
                                            <option value="{{ $dep->dep_id }}">{{ $dep->dep_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button name="status" value="2" class="btn btn-danger">Reject</button>
                                    <button name="status" value="1" class="btn btn-success">Approve & Open Ticket</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection