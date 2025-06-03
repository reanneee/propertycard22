@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Add New Entity
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('entities.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="entity_name" class="form-label fw-semibold">
                               Entity Name
                            </label>
                            <input type="text" 
                                   name="entity_name" 
                                   id="entity_name"
                                   class="form-control @error('entity_name') is-invalid @enderror" 
                                   value="{{ old('entity_name') }}" 
                                   placeholder="Enter entity name"
                                   required>
                            @error('entity_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="branch_id" class="form-label fw-semibold">
                                Branch
                            </label>
                            <select name="branch_id" 
                                    id="branch_id"
                                    class="form-select @error('branch_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}"
                                        {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fund Cluster Selection -->
                        <div class="mb-4">
                            <label for="fund_cluster_id" class="form-label fw-semibold">
                             Fund Cluster
                            </label>
                            <select name="fund_cluster_id" 
                                    id="fund_cluster_id"
                                    class="form-select @error('fund_cluster_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Fund Cluster --</option>
                                @foreach($fundClusters as $cluster)
                                    <option value="{{ $cluster->id }}"
                                        {{ old('fund_cluster_id') == $cluster->id ? 'selected' : '' }}>
                                        {{ $cluster->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fund_cluster_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                     <div class="d-flex justify-content-center gap-3 mt-3">
    <button type="submit" class="btn btn-success flex-fill" style="max-width: 150px;">
       Save Entity
    </button>
    <a href="{{ route('entities.index') }}" class="btn btn-outline-secondary flex-fill" style="max-width: 150px;">
     Back to Entities
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