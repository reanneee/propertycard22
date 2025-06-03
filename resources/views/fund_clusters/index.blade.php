@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Enhanced Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-dark fw-bold">
                <i class="fas fa-layer-group me-2 text-success"></i>
                Fund Clusters
            </h2>
            <p class="text-muted mb-0">Manage your fund cluster groups</p>
        </div>
        <a href="{{ route('fund_clusters.create') }}" class="btn btn-success btn-lg shadow-sm px-4">
            <i class="fas fa-plus me-2"></i>Add New
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <div class="flex-grow-1">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Enhanced Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 text-dark fw-semibold">
                <i class="fas fa-list me-2 text-success"></i>
                Fund Cluster List
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold border-0">
                                <i class="fas fa-tag me-2"></i>Name
                            </th>
                            <th class="px-4 py-3 text-muted fw-semibold border-0 text-center">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fundClusters as $cluster)
                        <tr class="border-0">
                            <td class="px-4 py-3 align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success-subtle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-layer-group text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-medium">{{ $cluster->name }}</h6>
                                        <small class="text-muted">Fund Cluster</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle text-center">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="{{ route('fund_clusters.edit', $cluster) }}" 
                                       class="btn btn-primary btn-sm px-3 py-2">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('fund_clusters.destroy', $cluster) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this fund cluster?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm px-3 py-2">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Success color variations */
.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.text-success {
    color: #198754 !important;
}

.border-success-subtle {
    border-color: rgba(25, 135, 84, 0.2) !important;
}

/* Avatar circle for cluster icons */
.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* Enhanced card styling */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f8fff9 0%, #f1fff3 100%);
}

/* Table enhancements */
.table-hover tbody tr:hover {
    background-color: rgba(25, 135, 84, 0.04);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.table thead th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Button enhancements */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    border: none;
    box-shadow: 0 2px 4px rgba(25, 135, 84, 0.2);
}

.btn-success:hover {
    background: linear-gradient(135deg, #157347 0%, #146c43 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0954c7 100%);
    transform: translateY(-1px);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
    transform: translateY(-1px);
}

.btn-group {
    border-radius: 8px;
    overflow: hidden;
}

/* Alert enhancements */
.alert-success {
    background: linear-gradient(135deg, #d1eddb 0%, #c3e6cb 100%);
    border-left: 4px solid #198754;
    border-radius: 8px;
}

.alert-success .fas {
    font-size: 1.1rem;
}

/* Shadow enhancements */
.shadow-sm {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

/* Header styling */
h2 {
    color: #2c3e50;
}

.text-muted {
    color: #6c757d !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 8px !important;
        margin-bottom: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
    
    .avatar-circle {
        width: 35px;
        height: 35px;
    }
    
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}

/* Smooth animations */
* {
    transition: all 0.2s ease;
}

/* Typography improvements */
.fw-semibold {
    font-weight: 600;
}

.fw-medium {
    font-weight: 500;
}

/* Custom alert dismiss button */
.alert .btn-close {
    padding: 0.75rem;
}
</style>
@endsection