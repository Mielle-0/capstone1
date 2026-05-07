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

        <div class="mb-4">
            <label for="fbk_disapprove_details" class="form-label fw-bold">
                <i class="fas fa-exclamation-circle me-2 text-danger"></i>Disapproval Details
            </label>
            <textarea 
                name="fbk_disapprove_details" 
                id="fbk_disapprove_details" 
                class="form-control shadow-sm" 
                rows="4" 
                placeholder="If you intend to drop this feedback, please explain why here..."></textarea>
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
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-lg px-4 fw-bold" onclick="return confirmDrop()">
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
    });


    // Clicking drop feedback button
    function confirmDrop() {
        const reason = document.getElementById('fbk_disapprove_details').value.trim();
        if (reason.length < 5) {
            alert("Please provide a detailed reason for disapproval.");
            return false;
        }
        return confirm("Are you sure you want to drop this feedback? This action is permanent.");
    }


    // Clicking create ticket button
    const approveBtn = document.getElementById('approve-btn');
    const mainForm = approveBtn.closest('form');

    approveBtn.addEventListener('click', function() {
        if (tagify.value.length === 0) {
            alert("Please select at least one department before creating a ticket.");
            
            // Optional: Bring focus back to the input to help the user
            tagify.DOM.input.focus(); 
            return;
        }

        // If validation passes, manually submit the form
        // We append a hidden input so the controller still gets the 'action' => 'approve'
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'action';
        hiddenInput.value = 'approve';
        mainForm.appendChild(hiddenInput);

        mainForm.submit();
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