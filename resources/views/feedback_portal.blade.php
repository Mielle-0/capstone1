<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<body class="bg-light">

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-header px-5">
    <div class="container-fluid ml-10vw d-block">
        <img src="{{ asset('images/um_logo.webp') }}" alt="Logo" height="40" class="me-1">
        <a class="navbar-brand" href="#"> | Feedback Portal</a>
    </div>
</nav>


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

        <form method="POST" action="/submit-feedback">
            @csrf

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-custom text-white">
                    <span class="fs-6 mb-0"><i class="fa fa-id-badge"></i> Personal Details</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ID Number</label>
                            <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">College/Program</label>
                            <input type="text" name="college_program" class="form-control" value="{{ old('college_program') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="branch" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch" id="branch" class="form-select @error('branch') is-invalid @enderror">
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->branch_id }}" {{ old('branch') == $b->branch_id ? 'selected' : '' }}>
                                        {{ $b->branch_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Initial</label>
                            <input type="text" name="middle_initial" class="form-control" maxlength="1" value="{{ old('middle_initial') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-Mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number (11 digits) <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-custom text-white">
                    <span class="fs-6 mb-0"><i class="fa fa-comment"></i> Your Feedback</span>
                </div>
                <div class="card-body">
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

                    <div class="form-check mb-3 d-flex align-items-center">
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
                    <!-- <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1">
                        <label class="form-check-label" for="consent">I agree to allow my feedback to be reviewed and processed by staff.</label>
                    </div> -->

                    <!-- Submit Button triggers modal -->
                    <!-- <button type="button" class="btn btn-primary" style="background:maroon;" data-bs-toggle="modal" data-bs-target="#verificationModal">
                        <i class="fa fa-paper-plane"></i>
                        Submit Feedback
                    </button> -->
                    <button type="submit" class="btn btn-success">
                        TEST SUBMIT (No Verification)
                    </button>
                </div>
            </div>


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

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="verificationModalLabel">Verify Your Identity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>A verification code was sent to your email and phone number. Please enter it below:</p>
        <input type="text" class="form-control" placeholder="Enter verification code">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Verify & Submit</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
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


</body>
</html>
