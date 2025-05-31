@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Entities</h2>
    <a href="{{ route('entities.create') }}" class="btn btn-success mb-3">Add New Entity</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @foreach($entities as $entity)
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            {{ $entity->entity_name }}
                            <small class="text-muted">(ID: {{ $entity->entity_id }})</small>
                        </h5>
                        <div>
                            <a href="{{ route('entities.show', $entity->entity_id) }}" class="btn btn-info btn-sm">View Details</a>
                            <a href="{{ route('entities.edit', $entity->entity_id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('entities.destroy', $entity->entity_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Branch:</strong> {{ $entity->branch->branch_name ?? 'N/A' }}</p>
                                <p><strong>Fund Cluster:</strong> {{ $entity->fundCluster->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Equipment Records:</strong> {{ $entity->receivedEquipments->count() }}</p>
                                <p><strong>Total Items:</strong> 
                                    {{ $entity->receivedEquipments->sum(function($equipment) {
                                        return $equipment->descriptions->sum(function($description) {
                                            return $description->items->count();
                                        });
                                    }) }}
                                </p>
                            </div>
                        </div>

                        @if($entity->receivedEquipments->count() > 0)
                            <h6 class="mt-3">Recent Equipment:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>PAR No.</th>
                                            <th>Date Acquired</th>
                                            <th>Amount</th>
                                            <th>Descriptions</th>
                                            <th>Total Items</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($entity->receivedEquipments->take(3) as $equipment)
                                            <tr>
                                                <td>{{ $equipment->par_no ?? 'N/A' }}</td>
                                                <td>{{ $equipment->date_acquired ? $equipment->date_acquired->format('M d, Y') : 'N/A' }}</td>
                                                <td>₱{{ number_format($equipment->amount ?? 0, 2) }}</td>
                                                <td>{{ $equipment->descriptions->count() }}</td>
                                                <td>{{ $equipment->descriptions->sum(function($desc) { return $desc->items->count(); }) }}</td>
                                            </tr>
                                        @endforeach
                                        @if($entity->receivedEquipments->count() > 3)
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <small class="text-muted">... and {{ $entity->receivedEquipments->count() - 3 }} more equipment records</small>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mt-3">No equipment records found for this entity.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Entities Management</h2>
            <p class="text-muted mb-0">Manage your organization entities and equipment records</p>
        </div>
        <a href="{{ route('entities.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Add New Entity
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Entities Cards -->
    @if($entities->count() > 0)
        <div class="row">
            @foreach($entities as $entity)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <!-- Card Header -->
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 text-primary">{{ $entity->entity_name }}</h5>
                                    <small class="text-muted">Entity ID: {{ $entity->entity_id }}</small>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('entities.show', $entity->entity_id) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('entities.edit', $entity->entity_id) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('entities.destroy', $entity->entity_id) }}" 
                                          method="POST" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this entity? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Entity Information -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="text-muted small">Branch</label>
                                        <p class="mb-0 fw-medium">{{ $entity->branch->branch_name ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="text-muted small">Fund Cluster</label>
                                        <p class="mb-0 fw-medium">{{ $entity->fundCluster->name ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="text-muted small">Equipment Records</label>
                                        <p class="mb-0 fw-medium">
                                            <span class="badge bg-primary">{{ $entity->receivedEquipments->count() }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="text-muted small">Total Items</label>
                                        <p class="mb-0 fw-medium">
                                            <span class="badge bg-success">
                                                {{ $entity->receivedEquipments->sum(function($equipment) {
                                                    return $equipment->descriptions->sum(function($description) {
                                                        return $description->items->count();
                                                    });
                                                }) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Equipment Section -->
                            @if($entity->receivedEquipments->count() > 0)
                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-tools me-2 text-muted"></i>Recent Equipment
                                        </h6>
                                        @if($entity->receivedEquipments->count() > 3)
                                            <small class="text-muted">
                                                Showing 3 of {{ $entity->receivedEquipments->count() }} records
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0">PAR No.</th>
                                                    <th class="border-0">Date Acquired</th>
                                                    <th class="border-0">Amount</th>
                                                    <th class="border-0 text-center">Descriptions</th>
                                                    <th class="border-0 text-center">Items</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($entity->receivedEquipments->take(3) as $equipment)
                                                    <tr>
                                                        <td>
                                                            <code class="text-primary">{{ $equipment->par_no ?? 'N/A' }}</code>
                                                        </td>
                                                        <td>
                                                            @if($equipment->date_acquired)
                                                                {{ $equipment->date_acquired->format('M d, Y') }}
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="fw-medium text-success">
                                                                ₱{{ number_format($equipment->amount ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark">
                                                                {{ $equipment->descriptions->count() }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark">
                                                                {{ $equipment->descriptions->sum(function($desc) { 
                                                                    return $desc->items->count(); 
                                                                }) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    @if($entity->receivedEquipments->count() > 3)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('entities.show', $entity->entity_id) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>
                                                View All {{ $entity->receivedEquipments->count() }} Equipment Records
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4 border-top">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">No equipment records found for this entity</p>
                                        <small>Equipment records will appear here once added</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-building fa-4x text-muted opacity-50"></i>
                        </div>
                        <h4 class="mb-3">No Entities Found</h4>
                        <p class="text-muted mb-4">
                            Get started by creating your first entity to manage equipment records and inventory.
                        </p>
                        <a href="{{ route('entities.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus me-2"></i>Create Your First Entity
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.info-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    height: 100%;
}

.info-item label {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
    display: block;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

.btn-group .btn {
    border-radius: 0.25rem !important;
    margin-left: 0.25rem;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

code {
    font-size: 0.875rem;
    background: rgba(13, 110, 253, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}
</style>
@endpush
@endsection
    </div>

    @if($entities->isEmpty())
        <div class="alert alert-info">
            No entities found. <a href="{{ route('entities.create') }}">Create your first entity</a>
        </div>
    @endif
</div>
@endsection