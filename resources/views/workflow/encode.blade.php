@extends('layouts.app')

@section('title', 'Encode Feedback')
@section('content')
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-11">
            <form action="{{ route('workflow.store') }}" method="POST" id="encodeForm">
                @csrf

                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 font-weight-bold" style="color: maroon;">
                            <i class="fas fa-user-circle me-2"></i>1. Personal Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ID Number</label>
                                <input type="text" name="id_number" class="form-control" placeholder="Student/Employee ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">College / Program</label>
                                <input type="text" name="college_program" class="form-control" placeholder="e.g. BSCS">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-select" required>
                                    <option value="">-- Select Branch --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_id }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">M.I.</label>
                                <input type="text" name="middle_initial" class="form-control" maxlength="1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 font-weight-bold" style="color: maroon;">
                            <i class="fas fa-tags me-2"></i>2. Select Feedback Type
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($types as $type)
                            <div class="col">
                                <input type="radio" class="btn-check type-selector" name="typ_id" 
                                       id="btn-type-{{ $type->typ_id }}" value="{{ $type->typ_id }}" 
                                       autocomplete="off" required>
                                <label class="btn btn-outline-maroon w-100 py-3 fw-bold" for="btn-type-{{ $type->typ_id }}">
                                    <i class="fas fa-comment-dots d-block mb-1"></i>
                                    {{ $type->typ_value }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <div id="thematic-container" class="mt-4 p-4 border rounded bg-light d-none">
                            <label class="form-label small fw-bold text-secondary mb-3 text-uppercase">Select Thematic Value:</label>
                            <div id="thematic-list" class="row g-2">
                                </div>
                            <input type="hidden" name="thm_id" id="selected_thm_id" required>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 font-weight-bold" style="color: maroon;">
                            <i class="fas fa-pen-fancy me-2"></i>3. Feedback Message
                        </h6>
                    </div>
                    <div class="card-body">
                        <textarea name="message" class="form-control" rows="5" placeholder="Describe the feedback received in detail..." required></textarea>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <button type="submit" class="btn btn-maroon btn-lg px-5 shadow rounded-pill">
                        <i class="fas fa-paper-plane me-2"></i>Complete Encoding
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Maroon Palette */
    .btn-maroon {
        background-color: maroon;
        color: white;
        border: none;
    }
    .btn-maroon:hover {
        background-color: #600000;
        color: white;
    }
    .btn-outline-maroon {
        color: maroon;
        border-color: maroon;
    }
    .btn-outline-maroon:hover {
        background-color: maroon;
        color: white;
    }
    .btn-check:checked + .btn-outline-maroon {
        background-color: maroon;
        color: white;
        box-shadow: 0 4px 8px rgba(128, 0, 0, 0.2);
    }

    /* Thematic Pill Styles */
    .theme-btn {
        background-color: white;
        border: 1px solid #dee2e6;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .theme-btn:hover {
        border-color: maroon;
        background-color: #fff5f5;
    }
    .theme-btn.active {
        background-color: #ffd700 !important; /* Gold highlight */
        color: maroon !important;
        border-color: maroon !important;
        font-weight: bold;
    }

    .form-control:focus, .form-select:focus {
        border-color: maroon;
        box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.1);
    }
</style>

<script>
    const allThemes = @json($themes);
    const container = document.getElementById('thematic-container');
    const list = document.getElementById('thematic-list');
    const thmInput = document.getElementById('selected_thm_id');

    document.querySelectorAll('.type-selector').forEach(radio => {
        radio.addEventListener('change', function() {
            const typeId = this.value;
            const filtered = allThemes.filter(t => t.typ_id == typeId);
            
            list.innerHTML = '';
            thmInput.value = '';
            container.classList.remove('d-none');

            filtered.forEach(theme => {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-6';
                col.innerHTML = `
                    <button type="button" class="btn theme-btn w-100 py-2 shadow-sm" data-id="${theme.thm_id}">
                        ${theme.thm_value}
                    </button>
                `;
                list.appendChild(col);
            });

            // Re-attach listeners to new buttons
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.theme-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    thmInput.value = this.getAttribute('data-id');
                });
            });
        });
    });
</script>
@endsection