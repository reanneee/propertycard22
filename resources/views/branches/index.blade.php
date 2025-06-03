@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Enhanced Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-dark fw-bold">
                <i class="fas fa-code-branch me-2 text-primary"></i>
                Branches
            </h2>
            <p class="text-muted mb-0">Manage your organization branches</p>
        </div>
        <a href="{{ route('branches.create') }}" class="btn btn-primary btn-lg shadow-sm px-4">
            <i class="fas fa-plus me-2"></i>Add Branch
        </a>
    </div>

    <!-- Enhanced Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 text-dark fw-semibold">
                <i class="fas fa-list me-2 text-primary"></i>
                Branch List
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold border-0">
                                <i class="fas fa-hashtag me-2"></i>ID
                            </th>
                            <th class="px-4 py-3 text-muted fw-semibold border-0">
                                <i class="fas fa-building me-2"></i>Name
                            </th>
                            <th class="px-4 py-3 text-muted fw-semibold border-0 text-center">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                        <tr class="border-0">
                            <td class="px-4 py-3 align-middle">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-medium">
                                    {{ $branch->branch_id }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary-subtle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-medium">{{ $branch->branch_name }}</h6>
                                        <small class="text-muted">Branch Office</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle text-center">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="{{ route('branches.edit', $branch->branch_id) }}" 
                                       class="btn btn-warning btn-sm px-3 py-2">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('branches.destroy', $branch->branch_id) }}" 
                                          method="POST" 
                                          style="display:inline"
                                          onsubmit="return confirm('Are you sure you want to delete this branch?')">
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
/* Primary color variations */
.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.text-primary {
    color: #0d6efd !important;
}

.border-primary-subtle {
    border-color: rgba(13, 110, 253, 0.2) !important;
}

/* Avatar circle for branch icons */
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
    background: linear-gradient(135deg, #f8f9ff 0%, #f1f3ff 100%);
}

/* Table enhancements */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.04);
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

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0954c7 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffb700 100%);
    border: none;
    color: #000;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #ffb700 0%, #ff9800 100%);
    transform: translateY(-1px);
    color: #000;
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

/* Badge styling */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
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
</style>
@endsection