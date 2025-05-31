@extends('layouts.app')

@section('title', 'Inventory Form #' . $inventoryForm->id)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i>
                        Inventory Form #{{ $inventoryForm->id }}
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('inventory_count_form.index') }}">Inventory Forms</a>
                            </li>
                            <li class="breadcrumb-item active">Form #{{ $inventoryForm->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('inventory_count_form.edit', $inventoryForm->id) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i>
                        Print
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-download mr-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('inventory_count_form.export', ['id' => $inventoryForm->id, 'format' => 'pdf']) }}">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    Export as PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('inventory_count_form.export', ['id' => $inventoryForm->id, 'format' => 'excel']) }}">
                                    <i class="fas fa-file-excel mr-2"></i>
                                    Export as Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Details Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        Form Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 40%;">Title:</td>
                                    <td>{{ $inventoryForm->title ?? 'Untitled' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Entity:</td>
                                    <td>
                                        <span class="badge bg-secondary fs-6">
                                            {{ $inventoryForm->entity_name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Fund:</td>
                                    <td>
                                        @if($inventoryForm->account_title)
                                            <strong>{{ $inventoryForm->account_title }}</strong><br>
                                            <small class="text-muted">Code: {{ $inventoryForm->account_code }}</small>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Inventory Date:</td>
                                    <td>
                                        @if($inventoryForm->inventory_date)
                                            <span class="badge bg-info">
                                                {{ \Carbon\Carbon::parse($inventoryForm->inventory_date)->format('F d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 40%;">Prepared By:</td>
                                    <td>
                                        <div>{{ $inventoryForm->prepared_by_name ?? 'Not specified' }}</div>
                                        @if($inventoryForm->prepared_by_position)
                                            <small class="text-muted">{{ $inventoryForm->prepared_by_position }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Reviewed By:</td>
                                    <td>
                                        <div>{{ $inventoryForm->reviewed_by_name ?? 'Not specified' }}</div>
                                        @if($inventoryForm->reviewed_by_position)
                                            <small class="text-muted">{{ $inventoryForm->reviewed_by_position }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ \Carbon\Carbon::parse($inventoryForm->created_at)->format('M d, Y g:i A') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Items:</td>
                                    <td>
                                        <span class="badge bg-success fs-6">
                                            {{ $items->total() }} items
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes mr-2"></i>
                        Inventory Items ({{ $items->total() }})
                    </h5>
                    <a href="{{ route('inventory_count_form.add_items', $inventoryForm->id) }}" 
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Add Items
                    </a>
                </div>

                <div class="card-body p-0">
                    @if($items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Property No.</th>
                                        <th width="25%">Description</th>
                                        <th width="10%" class="text-center">Qty</th>
                                        <th width="10%" class="text-center">Amount</th>
                                        <th width="10%">Condition</th>
                                        <th width="15%">Location</th>
                                        <th width="10%">Article</th>
                                        <th width="5%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $item->property_no }}</div>
                                                @if($item->new_property_no)
                                                    <small class="text-success">
                                                        New: {{ $item->new_property_no }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $item->description }}</div>
                                                @if($item->date_acquired)
                                                    <small class="text-muted">
                                                        Acquired: {{ \Carbon\Carbon::parse($item->date_acquired)->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-6">{{ $item->qty_physical }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($item->amount)
                                                    <span class="fw-bold text-success">
                                                        ₱{{ number_format($item->amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $conditionClass = match(strtolower($item->condition)) {
                                                        'good', 'excellent' => 'bg-success',
                                                        'fair', 'working' => 'bg-warning',
                                                        'poor', 'damaged' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $conditionClass }}">
                                                    {{ ucfirst($item->condition) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $item->location }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $item->article }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-info" 
                                                            onclick="showItemDetails({{ $inventoryForm->id }}, {{ $item->item_id }})"
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('property_cards.edit', $item->property_card_id) }}" 
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($item->remarks)
                                            <tr class="table-light">
                                                <td colspan="8">
                                                    <small class="text-muted">
                                                        <i class="fas fa-comment mr-1"></i>
                                                        <strong>Remarks:</strong> {{ $item->remarks }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            {{ $items->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No items found</h5>
                            <p class="text-muted">This inventory form doesn't have any items yet.</p>
                            <a href="{{ route('inventory_count_form.add_items', $inventoryForm->id) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i>
                                Add Items
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($items->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Items</h6>
                                <h3 class="mb-0">{{ $items->total() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-boxes fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Value</h6>
                                <h3 class="mb-0">₱{{ number_format($items->sum('amount'), 2) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-peso-sign fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Good Condition</h6>
                                <h3 class="mb-0">
                                    {{ $items->filter(function($item) { 
                                        return in_array(strtolower($item->condition), ['good', 'excellent']); 
                                    })->count() }}
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Needs Attention</h6>
                                <h3 class="mb-0">
                                    {{ $items->filter(function($item) { 
                                        return in_array(strtolower($item->condition), ['fair', 'poor', 'damaged']); 
                                    })->count() }}
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Item Details Modal -->
<div class="modal fade" id="itemDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Item Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="itemDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showItemDetails(inventoryFormId, itemId) {
    const modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
    const content = document.getElementById('itemDetailsContent');
    
    // Show loading spinner
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch item details
    fetch(`/inventory_count_form/${inventoryFormId}/items/${itemId}/details`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            content.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            return;
        }
        
        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Basic Information</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">Property No:</td>
                            <td>${data.property_no}</td>
                        </tr>
                        ${data.new_property_no ? `
                        <tr>
                            <td class="fw-bold">New Property No:</td>
                            <td><span class="text-success">${data.new_property_no}</span></td>
                        </tr>
                        ` : ''}
                        <tr>
                            <td class="fw-bold">Description:</td>
                            <td>${data.description}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Serial No:</td>
                            <td>${data.serial_no || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Unit:</td>
                            <td>${data.unit || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PAR No:</td>
                            <td>${data.par_no || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Inventory Details</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">Physical Quantity:</td>
                            <td><span class="badge bg-info">${data.physical_quantity}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Original Quantity:</td>
                            <td><span class="badge bg-secondary">${data.original_quantity}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Amount:</td>
                            <td>${data.amount ? '₱' + parseFloat(data.amount).toLocaleString() : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Condition:</td>
                            <td><span class="badge bg-${getConditionBadgeClass(data.condition)}">${data.condition}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Article:</td>
                            <td>${data.article || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Location:</td>
                            <td>${data.location}</td>
                        </tr>
                    </table>
                </div>
            </div>
            ${data.date_acquired ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="fw-bold mb-2">Additional Information</h6>
                    <p><strong>Date Acquired:</strong> ${new Date(data.date_acquired).toLocaleDateString()}</p>
                </div>
            </div>
            ` : ''}
            ${data.remarks ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="fw-bold mb-2">Remarks</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-comment mr-2"></i>
                        ${data.remarks}
                    </div>
                </div>
            </div>
            ` : ''}
            ${data.issue_transfer_disposal ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="fw-bold mb-2">Issue/Transfer/Disposal</h6>
                    <p>${data.issue_transfer_disposal}</p>
                    ${data.received_by_name ? `<p><strong>Received by:</strong> ${data.received_by_name}</p>` : ''}
                </div>
            </div>
            ` : ''}
        `;
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = '<div class="alert alert-danger">Error loading item details.</div>';
    });
}

function getConditionBadgeClass(condition) {
    const lowerCondition = condition.toLowerCase();
    if (['good', 'excellent'].includes(lowerCondition)) return 'success';
    if (['fair', 'working'].includes(lowerCondition)) return 'warning';
    if (['poor', 'damaged'].includes(lowerCondition)) return 'danger';
    return 'secondary';
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .breadcrumb, .card-header .btn {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        border: 1px solid #000 !important;
    }
    
    .table th, .table td {
        border: 1px solid #000 !important;
        padding: 8px !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
        background: white !important;
        color: black !important;
    }
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.8em;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.opacity-50 {
    opacity: 0.5;
}
</style>
@endpush