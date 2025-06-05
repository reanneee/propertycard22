@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add Fund Cluster</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('fund_clusters.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Fund Cluster Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter fund cluster name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <button type="submit" class="btn btn-success flex-fill custom-btn" style="max-width: 150px;">
                                Save Fund Cluster
                            </button>
                            <a href="{{ route('fund_clusters.index') }}" class="btn btn-outline-secondary flex-fill custom-btn" style="max-width: 150px;">
                                Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%) !important;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.custom-btn {
    padding: 6px 12px;
    font-size: 0.875rem;
    line-height: 1.5;
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08) !important;
}
</style>
@endsection
