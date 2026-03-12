{{-- resources/views/admin/ai-settings.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage AI Settings')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.settings.ai.update') }}" method="POST">
                @csrf
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-maroon text-white py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i> AI Prediction Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Confidence Threshold</label>
                                <span class="badge bg-maroon" style="font-size: 1rem;">
                                    <span id="thresholdVal">{{ $threshold * 100 }}</span>%
                                </span>
                            </div>
                            <input type="range" name="prediction_threshold" class="form-range custom-range" 
                                   min="0" max="1" step="0.05" value="{{ $threshold }}"
                                   oninput="document.getElementById('thresholdVal').innerText = (this.value * 100).toFixed(0)">
                            <div class="form-text mt-2 text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Departments suggested by the AI will only be visible to staff if the prediction probability is equal to or higher than this percentage.
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="fw-bold d-block">Enable AI Suggestions</label>
                                <small class="text-muted">Turn off to hide all AI recommendations globally.</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input custom-switch" type="checkbox" name="ai_enabled" 
                                       value="yes" {{ $aiEnabled ? 'checked' : '' }} style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light p-3 text-end">
                        <button type="submit" class="btn btn-maroon px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-maroon { background-color: #800000 !important; }
    .btn-maroon { background-color: #800000; color: white; border: none; }
    .btn-maroon:hover { background-color: #600000; color: white; }
    .form-range::-webkit-slider-thumb { background: #800000; }
    .form-range::-moz-range-thumb { background: #800000; }
    .form-check-input:checked { background-color: #800000; border-color: #800000; }
</style>
@endsection