@extends('layouts.portal-layout')

@section('title', 'Feedback Portal')

@section('content')

<div class="container mt-3">

    <!-- Carousel -->
    <div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/carousel1.png') }}" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/carousel2.png') }}" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/carousel3.png') }}" class="d-block w-100" alt="Slide 3">
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <!-- Content -->
    <div class="container py-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ session('step_one_complete') ? route('feedback.submit') : route('feedback.sendCode') }}">
            @csrf

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-custom text-white">
                    <span class="fs-6 mb-0"><i class="fa fa-id-badge"></i> Personal Details</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ID Number</label>
                            <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ session('personal_info.id_number', old('id_number')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">College/Program</label>
                            <input type="text" name="college_program" class="form-control" value="{{ session('personal_info.college_program', old('college_program')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="branch" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch" id="branch" class="form-select @error('branch') is-invalid @enderror" {{ session('step_one_complete') ? 'disabled' : '' }}>
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->branch_id }}" {{ session('personal_info.branch', old('branch')) == $b->branch_id ? 'selected' : '' }}>
                                        {{ $b->branch_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ session('personal_info.last_name', old('last_name')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ session('personal_info.first_name', old('first_name')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Initial</label>
                            <input type="text" name="middle_initial" class="form-control" maxlength="1" value="{{ session('personal_info.middle_initial', old('middle_initial')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-Mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="userEmail" class="form-control @error('email') is-invalid @enderror" value="{{ session('personal_info.email', old('email')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number (11 digits) <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ session('personal_info.phone', old('phone')) }}" {{ session('step_one_complete') ? 'disabled' : '' }}>
                        </div>
                    </div>

                    @if(!session('step_one_complete'))
                        <div class="mt-4 d-flex justify-content-between align-items-center flex-row-reverse">
                            

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-envelope"></i> Send Verification Code
                            </button>


                            <button type="button" id="btnRequestHistory" class="btn btn-outline-secondary d-none">
                                <i class="fa fa-history"></i> Email My Feedback History
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if(session('step_one_complete'))
            <div class="card mb-4 shadow-sm border-primary">
                <div class="card-header bg-custom text-white">
                    <span class="fs-6 mb-0"><i class="fa fa-comment"></i> Your Feedback & Verification</span>
                </div>
                <div class="card-body">
                    
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fa fa-info-circle me-2"></i>
                        <div>
                            A 6-digit verification code has been sent to your contact details. Please enter it below to submit your feedback.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Verification Code <span class="text-danger">*</span></label>
                        <input type="text" name="verification_code" class="form-control form-control-lg w-50 @error('verification_code') is-invalid @enderror" placeholder="Enter 6-digit code" required>
                        @error('verification_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="mb-4">

                    <div class="mb-3">
                        <label class="form-label">Select Feedback Type <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            @foreach($types as $type)
                                <input type="radio" class="btn-check" name="category" 
                                    id="cat-{{ $type->typ_id }}" value="{{ $type->typ_id }}" 
                                    {{ old('category') == $type->typ_id ? 'checked' : '' }} autocomplete="off">
                                <label class="btn btn-outline-primary" for="cat-{{ $type->typ_id }}">{{ $type->typ_value }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category-select" class="form-label">Specific Category/Theme <span class="text-danger">*</span></label>
                        <select id="category-select" name="thm_id" class="form-select @error('thm_id') is-invalid @enderror">
                            <option value="">Please select a feedback type above</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" rows="5" class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                    </div>

                    <div class="form-check mb-4 d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1" required>
                        <label class="form-check-label ms-2" for="consent">
                            I agree to allow my feedback to be reviewed and processed by staff.
                        </label>
                        <span class="ms-2 text-muted" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Your feedback will be classified by our AI and forwarded to the relevant department head for action and quality monitoring.">
                            <i class="fa fa-question-circle" style="cursor: pointer;"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('feedback.form') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Edit Personal Details
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-paper-plane"></i> Submit Feedback
                        </button>
                    </div>
                </div>
            </div>
            @endif


        </form>

        <!-- FAQs -->
        <div class="card shadow-sm">
            <div class="card-header bg-custom text-white">
                <span class="fs-6 mb-0">
                    <i class="fa fa-question-circle"></i>
                    Frequently Asked Questions
                </span>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                Is my feedback anonymous?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You may submit feedback without entering your name or email.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                Who will see my feedback?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Your feedback will be reviewed by relevant staff based on classification.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                Can I get a response?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                If you leave your email, we may contact you for follow-up or clarification.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div> <!-- Wrapper -->

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<!-- <script src="{{ asset('js/feedback-portal.js') }}"></script> -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('userEmail');
    const historyBtn = document.getElementById('btnRequestHistory');

    if (!emailInput || !historyBtn) return;

    // Simple Regex for valid email format
    const isValidEmail = (email) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };

    // Listen for typing in the email field
    emailInput.addEventListener('input', function(e) {
        if (isValidEmail(e.target.value)) {
            historyBtn.classList.remove('d-none'); // Show button
        } else {
            historyBtn.classList.add('d-none');    // Hide button
        }
    });

    // Handle button click via AJAX so the form doesn't submit
    historyBtn.addEventListener('click', function() {
        const email = emailInput.value;
        const originalHtml = historyBtn.innerHTML;
        
        // UI Loading State
        historyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
        historyBtn.disabled = true;

        // Send background request
        fetch('{{ route("feedback.requestHistory") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            // UI Success State
            historyBtn.innerHTML = '<i class="fa fa-check"></i> Link Sent!';
            historyBtn.classList.replace('btn-outline-info', 'btn-success');
            
            // Reset button after 3 seconds
            setTimeout(() => {
                historyBtn.innerHTML = originalHtml;
                historyBtn.classList.replace('btn-success', 'btn-outline-info');
                historyBtn.disabled = false;
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            historyBtn.innerHTML = '<i class="fa fa-exclamation-circle"></i> Error Sending';
            historyBtn.classList.replace('btn-outline-info', 'btn-danger');
            
            setTimeout(() => {
                historyBtn.innerHTML = originalHtml;
                historyBtn.classList.replace('btn-danger', 'btn-outline-info');
                historyBtn.disabled = false;
            }, 3000);
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoryRadios = document.querySelectorAll('input[name="category"]');
    const themeSelect = document.getElementById('category-select');
    
    // This variable now contains your thm_id, typ_id, and thm_value rows
    const allThemes = @json($themes);

    categoryRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const selectedTypeId = radio.value; // This is the typ_id (1, 2, 3, 4, or 5)

            // 1. Filter the themes where the typ_id matches the selected radio button
            const filteredThemes = allThemes.filter(t => t.typ_id == selectedTypeId);

            // 2. Clear the current options
            themeSelect.innerHTML = '<option value="">-- Select Specific Category --</option>';
            
            // 3. Populate the dropdown with the filtered results
            filteredThemes.forEach(theme => {
                const opt = document.createElement('option');
                opt.value = theme.thm_id;    // Uses the thm_id (1-14)
                opt.textContent = theme.thm_value; // Uses the thm_value (Others, Process, etc.)
                themeSelect.appendChild(opt);
            });

            // 4. Enable the select box if options exist
            themeSelect.disabled = filteredThemes.length === 0;
        });
    });

    // Handle page reload/validation error state
    const initialChecked = document.querySelector('input[name="category"]:checked');
    if (initialChecked) {
        initialChecked.dispatchEvent(new Event('change'));
        
        // Bonus: Re-select the thm_id if it was previously selected (for validation errors)
        const oldThmId = "{{ old('thm_id') }}";
        if (oldThmId) {
            setTimeout(() => { themeSelect.value = oldThmId; }, 10);
        }
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

@endsection
