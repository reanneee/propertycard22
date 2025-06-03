@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Inventory Count Forms</h1>
                    <p class="text-muted mb-0">Manage and track all inventory forms and vacant equipment</p>
                </div>
                <div>
                 
                </div>
            </div>

            <!-- Summary Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $totalForms }}</h3>
                                    <p class="mb-0">Total Forms</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $completedForms }}</h3>
                                    <p class="mb-0">Completed Forms</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $totalVacantEquipment }}</h3>
                                    <p class="mb-0">Vacant Equipment</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">₱{{ number_format($totalVacantValue, 2) }}</h3>
                                    <p class="mb-0">Vacant Equipment Value</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        

            <!-- Main Data Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Inventory Forms Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="inventoryFormsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Form ID</th>
                                    <th>Entity</th>
                                    <th>Branch</th>
                                    <th>Inventory Date</th>
                                    <th>Status</th>
                                    <th>Total Items</th>
                                    <th>With Property Cards</th>
                                    <th>Vacant Equipment</th>
                                    <th>Vacant Value</th>
                                    <th>Completion %</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryForms as $form)
                                <tr data-entity-id="{{ $form->entity_id }}" 
                                    data-status="{{ $form->status }}" 
                                    data-vacant-count="{{ $form->vacant_equipment_count }}">
                                    <td>
                                        <span class="fw-bold text-primary">ICF-{{ str_pad($form->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $form->entity->entity_name ?? 'N/A' }}</span>
                                            <small class="text-muted">{{ $form->entity->fundCluster->name ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $form->entity->branch->branch_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($form->inventory_date)
                                            <span class="d-block">{{ \Carbon\Carbon::parse($form->inventory_date)->format('M d, Y') }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($form->inventory_date)->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $form->status === 'completed' ? 'success' : ($form->status === 'pending' ? 'warning' : 'secondary') }} fs-6">
                                            {{ ucfirst($form->status ?? 'draft') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info fs-6">{{ $form->total_items ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-success fs-6 mb-1">{{ $form->items_with_cards ?? 0 }}</span>
                                            @if($form->total_items > 0)
                                                <small class="text-muted">
                                                    {{ number_format(($form->items_with_cards / $form->total_items) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($form->vacant_equipment_count > 0)
                                            <span class="badge bg-warning fs-6">{{ $form->vacant_equipment_count }}</span>
                                        @else
                                            <span class="badge bg-light text-dark fs-6">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($form->vacant_equipment_value > 0)
                                            <span class="fw-medium text-warning">₱{{ number_format($form->vacant_equipment_value, 2) }}</span>
                                        @else
                                            <span class="text-muted">₱0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $percentage = $form->total_items > 0 ? ($form->items_with_cards / $form->total_items) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('inventory-count-form.show', $form->id) }}" 
                                               class="btn btn-outline-primary" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($form->vacant_equipment_count > 0)
                                                <button type="button" 
                                                        class="btn btn-outline-warning view-vacant-btn" 
                                                        data-form-id="{{ $form->id }}"
                                                        title="View Vacant Equipment">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            @endif
                                            @if(auth()->user()->can('edit', $form))
                                                <a href="{{ route('inventory-count-form.edit', $form->id) }}" 
                                                   class="btn btn-outline-secondary" 
                                                   title="Edit Form">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No Inventory Forms Found</h5>
                                            <p class="text-muted mb-4">Get started by creating your first inventory count form.</p>
                                            <a href="{{ route('inventory-count-form.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Create First Form
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vacant Equipment Modal -->
<div class="modal fade" id="vacantEquipmentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vacant Equipment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="vacantEquipmentContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportVacantEquipment()">
                    <i class="fas fa-download me-1"></i>Export Vacant Equipment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#inventoryFormsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[3, 'desc']], // Order by inventory date descending
        columnDefs: [
            { orderable: false, targets: -1 }, // Disable sorting on Actions column
            { type: 'date', targets: 3 }, // Date sorting for inventory date
            { type: 'num-fmt', targets: [5, 6, 7, 8] } // Numeric sorting for counts and values
        ],
        language: {
            search: "Search forms:",
            lengthMenu: "Show _MENU_ forms per page",
            info: "Showing _START_ to _END_ of _TOTAL_ forms",
            infoEmpty: "No forms available",
            infoFiltered: "(filtered from _MAX_ total forms)"
        }
    });

    // Filter functionality
    $('#entityFilter').on('change', function() {
        var selectedEntity = $(this).val();
        if (selectedEntity === '') {
            table.column(1).search('').draw();
        } else {
            // Filter by entity name
            var entityName = $(this).find('option:selected').text();
            table.column(1).search(entityName).draw();
        }
    });

    $('#statusFilter').on('change', function() {
        var selectedStatus = $(this).val();
        if (selectedStatus === '') {
            table.column(4).search('').draw();
        } else {
            table.column(4).search(selectedStatus).draw();
        }
    });

    $('#vacantFilter').on('change', function() {
        var selectedVacant = $(this).val();
        if (selectedVacant === '') {
            $.fn.dataTable.ext.search.pop(); // Remove custom search
        } else if (selectedVacant === 'with_vacant') {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var vacantCount = parseInt($(table.row(dataIndex).node()).data('vacant-count')) || 0;
                return vacantCount > 0;
            });
        } else if (selectedVacant === 'no_vacant') {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var vacantCount = parseInt($(table.row(dataIndex).node()).data('vacant-count')) || 0;
                return vacantCount === 0;
            });
        }
        table.draw();
    });

    // View vacant equipment
    $('.view-vacant-btn').on('click', function() {
        var formId = $(this).data('form-id');
        loadVacantEquipment(formId);
        $('#vacantEquipmentModal').modal('show');
    });

    // Auto-refresh every 5 minutes
    setInterval(function() {
        if (!$('.modal.show').length) {
            location.reload();
        }
    }, 300000);
});

// Reset all filters
function resetFilters() {
    $('#entityFilter').val('');
    $('#statusFilter').val('');
    $('#vacantFilter').val('');
    
    // Clear DataTable filters
    var table = $('#inventoryFormsTable').DataTable();
    table.search('').columns().search('').draw();
    
    // Remove custom search functions
    if ($.fn.dataTable.ext.search.length > 0) {
        $.fn.dataTable.ext.search.pop();
    }
    table.draw();
}

// Load vacant equipment details
function loadVacantEquipment(formId) {
    $('#vacantEquipmentContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading vacant equipment...</p>
        </div>
    `);

    $.ajax({
        url: `/inventory-count-form/${formId}/vacant-equipment`,
        method: 'GET',
        success: function(response) {
            $('#vacantEquipmentContent').html(response);
        },
        error: function() {
            $('#vacantEquipmentContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load vacant equipment data. Please try again.
                </div>
            `);
        }
    });
}


function showNotification(message, type = 'info') {
    const alertClass = `alert-${type}`;
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-triangle' : 
                     type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="fas ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
/* Card hover effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,.15);
}

/* Progress bar styling */
.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Badge enhancements */
.badge.fs-6 {
    font-size: 0.875rem !important;
    padding: 0.35em 0.65em;
}

/* Table enhancements */
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #343a40 !important;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

/* Empty state styling */
.empty-state {
    padding: 60px 20px;
}

.empty-state i {
    opacity: 0.5;
}

/* Modal enhancements */
.modal-xl {
    max-width: 1200px;
}

.modal-content {
    border: 0;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

/* Statistics cards */
.card.bg-primary,
.card.bg-success,
.card.bg-warning,
.card.bg-info {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card.bg-primary:hover,
.card.bg-success:hover,
.card.bg-warning:hover,
.card.bg-info:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Filter section */
.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body .row.g-3 > .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.775rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .empty-state {
        padding: 40px 15px;
    }
    
    .modal-xl {
        max-width: 95%;
        margin: 1rem auto;
    }
}

@media (max-width: 576px) {
    .badge.fs-6 {
        font-size: 0.75rem !important;
        padding: 0.25em 0.5em;
    }
    
    .progress {
        height: 16px !important;
    }
    
    .progress-bar {
        font-size: 0.7rem;
    }
    
    .btn-group-sm > .btn {
        padding: 0.2rem 0.35rem;
        font-size: 0.7rem;
    }
}

/* Print styles */
@media print {
    .no-print,
    .btn,
    .modal,
    .card .card-header .btn,
    .dropdown,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
    
    .table {
        font-size: 12px;
    }
    
    body {
        font-size: 12px;
        line-height: 1.3;
    }
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .table th {
        background-color: #000 !important;
        color: #fff !important;
    }
    
    .badge {
        border: 2px solid;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .card,
    .table-hover tbody tr,
    .progress-bar,
    .btn {
        transition: none;
    }
}
</style>
@endpush