@extends('layouts.app')

@section('title', 'Feedback Validation')
@section('content')

<div class="container py-4" style="max-width: 900px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Process Feedback</h3>
        <a href="{{ route('workflow.validation') }}" class="btn btn-outline-secondary">
            <i class="fas fa-chevron-left me-1"></i> Back to Queue
        </a>
    </div>

    <form action="{{ route('workflow.process', $feedback->fbk_id) }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light rounded">
                <div class="row">
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted text-uppercase">Sender</label>
                        <p class="mb-0 fw-bold fs-5">{{ $feedback->std_name }}</p>
                        <p class="text-muted small">{{ $feedback->std_id_no }} | {{ $feedback->std_email }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <label class="small fw-bold text-muted text-uppercase">Submitted On</label>
                        <p class="mb-0">{{ $feedback->fbk_date_created->format('M d, Y h:i A') }}</p>
                        <span class="badge bg-maroon rounded-pill">
                                {{ $feedback->branch_id }}
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold"><i class="fas fa-comment-dots me-2 text-maroon"></i>Feedback Message</label>
            <div class="p-4 bg-white border rounded shadow-sm fs-5" style="min-height: 150px; line-height: 0.7;">
                {{ $feedback->fbk_details }}
            </div>
        </div>

        <div class="card border-maroon shadow-sm mb-5">
            <div class="card-body p-4">
                <label class="form-label fw-bold fs-5 mb-3">
                    <i class="fas fa-share-nodes me-2 text-maroon"></i>Assign to Departments
                </label>
                
                <p class="text-muted small mb-3">Select one or more departments to handle this feedback. A separate ticket will be generated for each.</p>

                
                <script src="{{ asset('js/tagify.js') }}"></script>
                <script src="{{ asset('tagify.polyfills.min.js') }}"></script>
                <link href="{{ asset('css/tagify.css') }}" rel="stylesheet" type="text/css" />

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase">Assign Departments</label>
                    
                    <input name="dep_ids" 
                        id="dept-autocomplete" 
                        class="form-control" 
                        placeholder="Type department name..."
                        value='@json($candidates->map(fn($c) => ["value" => $c->dep_id, "name" => $c->dep_name]))'>
                </div>


            </div>
        </div>

        <div class="sticky-bottom bg-white border-top p-3 shadow-lg card" style="margin: 0 -1.5rem -1.5rem -1.5rem;">
            <div class="container" style="max-width: 900px;">
                <div class="d-flex gap-3">
                    <!-- <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-lg px-4 fw-bold" onclick="return confirmDrop()">
                        <i class="fas fa-trash me-1"></i> Drop Feedback
                    </button> -->
                    <button type="button" class="btn btn-outline-danger btn-lg px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#dropFeedbackModal">
                        <i class="fas fa-trash me-1"></i> Drop Feedback
                    </button>
                    <button type="submit" id="approve-btn" name="action" value="approve" class="btn btn-maroon btn-lg flex-grow-1 fw-bold" >
                        <i class="fas fa-ticket-alt me-1"></i> Create Tickets for Selected Departments
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- Drop Modal -->
<div class="modal fade" id="dropFeedbackModal" tabindex="-1" aria-labelledby="dropFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="dropFeedbackModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Drop Feedback
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3">You are about to drop this feedback. This action is permanent. Please provide a detailed reason below:</p>
                <textarea id="modal_disapprove_reason" class="form-control mb-2" rows="4" placeholder="Enter reason here (minimum 5 characters)..."></textarea>
                <div id="drop-error-msg" class="text-danger small d-none"><i class="fas fa-times-circle me-1"></i> Please provide a detailed reason (at least 5 characters).</div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-drop-btn" class="btn btn-danger px-4">Confirm Drop</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        var input = document.querySelector('#dept-autocomplete');
        var initialValue = input.value ? JSON.parse(input.value) : [];

        var tagify = new Tagify(input, {
            tagTextProp: 'name', // Show the department name in the chip
            enforceWhitelist: true,
            skipInvalid: true,
            whitelist: initialValue,
            dropdown: {
                closeOnSelect: true,
                enabled: 1, // Show suggestions after 1 character
                classname: 'dept-suggestions',
                searchKeys: ['name'] 
            },
            templates: {
                // This creates the "Grouped by Branch" look in the dropdown
                dropdownItem: function(item) {
                    return `
                        <div ${this.getAttributes(item)} class='tagify__dropdown__item'>
                            <strong class="text-maroon">${item.name}</strong>
                        </div>`;
                }
            }
        });

        // The AJAX "Typeahead" logic
        tagify.on('input', function(e) {
            var value = e.detail.value;

            // Show loading state
            tagify.loading(true);

            fetch("{{ route('departments.autocomplete') }}?query=" + value)
                .then(res => res.json())
                .then(function(newWhitelist) {
                    tagify.whitelist = newWhitelist; // update whitelist
                    tagify.loading(false).dropdown.show(value); // render the suggestions
                });
        });

        const approveBtn = document.getElementById('approve-btn');
        const mainForm = approveBtn.closest('form');

        // Check for multiple departments
        approveBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Stop standard form submission to allow checks

            if (tagify.value.length === 0) {
                alert("Please select a department before creating a ticket.");
                tagify.DOM.input.focus(); 
                return;
            }

            // Guard: Prevent multiple departments
            if (tagify.value.length > 1) {
                alert("Please select ONLY ONE department. Routing to multiple departments is not allowed.");
                return;
            }

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'action';
            hiddenInput.value = 'approve';
            mainForm.appendChild(hiddenInput);

            mainForm.submit();
        });



        // HTML Modal for Drop Details
        const confirmDropBtn = document.getElementById('confirm-drop-btn');
        const reasonInput = document.getElementById('modal_disapprove_reason');
        const errorMsg = document.getElementById('drop-error-msg');

        confirmDropBtn.addEventListener('click', function() {
            const reason = reasonInput.value.trim();

            // Validate the input
            if (reason.length < 5) {
                errorMsg.classList.remove('d-none');
                reasonInput.classList.add('is-invalid');
                return; // Stop the function here
            }

            // If validation passes, hide errors and proceed
            errorMsg.classList.add('d-none');
            reasonInput.classList.remove('is-invalid');

            // Create the hidden inputs needed by your controller
            const hiddenAction = document.createElement('input');
            hiddenAction.type = 'hidden';
            hiddenAction.name = 'action';
            hiddenAction.value = 'reject'; // run the 'reject' block

            const hiddenReason = document.createElement('input');
            hiddenReason.type = 'hidden';
            hiddenReason.name = 'fbk_disapprove_details'; // Matches your database/controller
            hiddenReason.value = reason;

            // Append to the main form and submit
            mainForm.appendChild(hiddenAction);
            mainForm.appendChild(hiddenReason);
            
            // Disable button to prevent double-clicking
            confirmDropBtn.disabled = true;
            confirmDropBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            mainForm.submit();
        });

        // Clear validation errors when the modal is closed so it's clean next time it opens
        document.getElementById('dropFeedbackModal').addEventListener('hidden.bs.modal', function () {
            reasonInput.value = '';
            reasonInput.classList.remove('is-invalid');
            errorMsg.classList.add('d-none');
        });

    });

    // Timeout for status messages
    setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000); // 5000ms = 5 seconds

</script>
@endpush

<style>
    .btn-maroon { background-color: #800000; color: white; }
    .btn-maroon:hover { background-color: #600000; color: white; }
    .border-maroon { border: 1px solid #800000; }
    .sticky-bottom { z-index: 1020; }
</style>
<style>
    /* Styling the chips to match your Maroon theme */
    .tagify__tag {
        --tag-bg: #800000;
        --tag-text-color: #fff;
        --tag-remove-btn-color: #fff;
        border-radius: 20px;
    }
    .tagify__tag:hover {
        --tag-bg: #600000;
    }
    .text-maroon { color: #800000; }
</style>
@endsection