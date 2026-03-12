@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">✍️ Encode New Feedback</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Student ID/No.</label>
                        <input type="text" name="std_id_no" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="std_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Feedback Type</label>
                        <select id="typ_id" name="typ_id" class="form-select" required>
                            <option value="">Select Type...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->typ_id }}">{{ $type->typ_value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thematic Area</label>
                        <select id="thm_id" name="thm_id" class="form-select" required disabled>
                            <option value="">Select Type First...</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Feedback Details</label>
                        <textarea name="fbk_details" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="fbk_attachment" class="form-control">
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="reset" class="btn btn-light border">Clear</button>
                        <button type="submit" class="btn btn-primary px-4">Submit Feedback</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Logic to filter themes based on selected type
    const themes = @json($themes);
    document.getElementById('typ_id').addEventListener('change', function() {
        const typeId = this.value;
        const themeSelect = document.getElementById('thm_id');
        themeSelect.innerHTML = '<option value="">Select Theme...</option>';
        
        if (typeId) {
            const filtered = themes.filter(t => t.typ_id == typeId);
            filtered.forEach(t => {
                themeSelect.innerHTML += `<option value="${t.thm_id}">${t.thm_value}</option>`;
            });
            themeSelect.disabled = false;
        } else {
            themeSelect.disabled = true;
        }
    });
</script>
@endsection