@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Count Form Details</h3>
                    <div class="card-tools">
                     
                        <a href="{{ route('inventory-count-form.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        
                    </div>
                </div>
                <div class="card-body">
                    <!-- Form Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h5 class="section-title">Entity Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Entity:</strong></td>
                                        <td>{{ $inventoryForm->entity->entity_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Branch:</strong></td>
                                        <td>{{ $inventoryForm->entity->branch->branch_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fund Cluster:</strong></td>
                                        <td>{{ $inventoryForm->entity->fundCluster->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fund Account Code:</strong></td>
                                        <td>{{ $inventoryForm->fund->account_code }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <h5 class="section-title">Form Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Inventory Date:</strong></td>
                                        <td>{{ $inventoryForm->formatted_inventory_date ?? 'Not Set' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td>{{ $inventoryForm->title ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Prepared By:</strong></td>
                                        <td>{{ $inventoryForm->prepared_by_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reviewed By:</strong></td>
                                        <td>{{ $inventoryForm->reviewed_by_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $inventoryForm->status === 'completed' ? 'success' : ($inventoryForm->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($inventoryForm->status ?? 'draft') }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                 <!-- Statistics -->
<div class="row mb-4">
    <!-- Total Items -->
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm rounded bg-white p-3 d-flex align-items-center">
            <span class="info-box-icon bg-info text-white me-3 p-3 rounded-circle">
                <i class="fas fa-boxes fa-lg"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-bold" title="Total number of inventory items">Total Items</span>
                <span class="info-box-number h5">{{ $totalItems }}</span>
            </div>
        </div>
    </div>

    <!-- With Property Cards -->
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm rounded bg-white p-3 d-flex align-items-center">
            <span class="info-box-icon bg-success text-white me-3 p-3 rounded-circle">
                <i class="fas fa-check fa-lg"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-bold" title="Items with registered property cards">With Property Cards</span>
                <span class="info-box-number h5">{{ $itemsWithPropertyCards }}</span>
            </div>
        </div>
    </div>

    <!-- Without Property Cards -->
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm rounded bg-white p-3 d-flex align-items-center">
            <span class="info-box-icon bg-warning text-dark me-3 p-3 rounded-circle">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-bold" title="Items missing property cards">Without Property Cards</span>
                <span class="info-box-number h5">{{ $itemsWithoutPropertyCards }}</span>
            </div>
        </div>
    </div>

    <!-- Total Value -->
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm rounded bg-white p-3 d-flex align-items-center">
            <span class="info-box-icon bg-primary text-white me-3 p-3 rounded-circle">
                <i class="fas fa-dollar-sign fa-lg"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-bold" title="Total value of all items">Total Value</span>
                <span class="info-box-number h5">₱{{ number_format($totalValue ?? 0, 2) }}</span>
            </div>
        </div>
    </div>
</div>


             

                    <!-- Inventory Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="inventoryTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="20%">Description</th>
                                    <th width="15%">Property No.</th>
                                    <th width="12%">Serial No.</th>
                                    <th width="8%">Unit</th>
                                    <th width="12%">Unit Value</th>
                                    <th width="10%">Qty (Physical)</th>
                                    <th width="15%">Location</th>
                                    <th width="8%">Condition</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryItems as $item)
                                <tr data-condition="{{ $item->condition }}">
                                    <td>
                                        <div class="item-description">
                                            {{ $item->article_description }}
                                            @if($item->article)
                                                <br><small class="text-muted">{{ $item->article }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="property-numbers">
                                            <span class="old-property-no">{{ $item->old_property_no }}</span>
                                            @if($item->new_property_no)
                                                <br><small class="text-primary">New: {{ $item->new_property_no }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="serial-no">{{ $item->serial_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $item->unit ?? 'N/A' }}</td>
                                    <td>
                                        <span class="unit-value">₱{{ number_format($item->unit_value, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($item->has_property_card)
                                            <span class="badge bg-success fs-6">{{ $item->quantity_per_physical_count }}</span>
                                        @else
                                            <span class="badge bg-warning fs-6">No Property Card</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="location-text">{{ $item->location_whereabouts }}</span>
                                    </td>
                                    <td>
                                        @if($item->condition)
                                            <span class="badge bg-{{ $item->condition == 'Good' ? 'success' : ($item->condition == 'Fair' ? 'warning' : 'danger') }}">
                                                {{ $item->condition }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('inventory-count-form.item-details', [$inventoryForm->id, $item->item_id]) }}" 
                                               class="btn btn-outline-primary" 
                                               title="View full item details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->can('edit', $inventoryForm))
                                            <button type="button" 
                                                    class="btn btn-outline-warning edit-item-btn" 
                                                    data-item-id="{{ $item->item_id }}"
                                                    title="Edit item">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No inventory items found.</p>
                                            @if(auth()->user()->can('edit', $inventoryForm))
                                            <a href="{{ route('inventory-count-forms.items.create', $inventoryForm->id) }}" 
                                               class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Add First Item
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Section -->
                    @if($inventoryItems->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="summary-section">
                                <h5 class="section-title">Summary</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Total Items:</strong></td>
                                                <td>{{ $totalItems }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Items with Property Cards:</strong></td>
                                                <td>{{ $itemsWithPropertyCards }} ({{ $totalItems > 0 ? number_format(($itemsWithPropertyCards / $totalItems) * 100, 1) : 0 }}%)</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Items without Property Cards:</strong></td>
                                                <td>{{ $itemsWithoutPropertyCards }} ({{ $totalItems > 0 ? number_format(($itemsWithoutPropertyCards / $totalItems) * 100, 1) : 0 }}%)</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Good Condition:</strong></td>
                                                <td>{{ $inventoryItems->where('condition', 'Good')->count() }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fair Condition:</strong></td>
                                                <td>{{ $inventoryItems->where('condition', 'Fair')->count() }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Poor Condition:</strong></td>
                                                <td>{{ $inventoryItems->where('condition', 'Poor')->count() }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with enhanced options
    var table = $('#inventoryTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: -1 }, // Disable sorting on Actions column
            { width: "20%", targets: 0 },
            { width: "15%", targets: 1 },
            { width: "12%", targets: 2 },
            { width: "8%", targets: 3 },
            { width: "12%", targets: 4 },
            { width: "10%", targets: 5 },
            { width: "15%", targets: 6 },
            { width: "8%", targets: 7 },
            { width: "10%", targets: 8 }
        ],
        language: {
            search: "Search items:",
            lengthMenu: "Show _MENU_ items per page",
            info: "Showing _START_ to _END_ of _TOTAL_ items",
            infoEmpty: "No items available",
            infoFiltered: "(filtered from _MAX_ total items)"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-outline-secondary btn-sm'
            },
            {
                extend: 'csv',
                className: 'btn btn-outline-success btn-sm'
            },
            {
                extend: 'excel',
                className: 'btn btn-outline-success btn-sm'
            },
            {
                extend: 'pdf',
                className: 'btn btn-outline-danger btn-sm'
            },
            {
                extend: 'print',
                className: 'btn btn-outline-primary btn-sm'
            }
        ]
    });

    // Condition filter functionality
    $('#conditionFilter').on('change', function() {
        var selectedCondition = $(this).val();
        if (selectedCondition === '') {
            table.column(7).search('').draw();
        } else {
            table.column(7).search(selectedCondition).draw();
        }
    });

    // Edit item functionality (if user has permission)
    $('.edit-item-btn').on('click', function() {
        const itemId = $(this).data('item-id');
        const inventoryFormId = {{ $inventoryForm->id }};
        window.location.href = `/inventory-count-form/${inventoryFormId}/item/${itemId}/edit`;
    });

    // Export functions
    window.exportToExcel = function() {
        table.button('.buttons-excel').trigger();
    };

    window.exportToPDF = function() {
        table.button('.buttons-pdf').trigger();
    };

    // Auto-refresh functionality (optional)
    @if(config('app.env') !== 'production')
    setInterval(function() {
        // Only refresh if there are no open modals or active operations
        if (!$('.modal.show').length && !$('.loading').length) {
            // You can implement auto-refresh logic here if needed
            // location.reload();
        }
    }, 300000); // 5 minutes
    @endif

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.keyCode === 80) {
            e.preventDefault();
            window.print();
        }
    });

    // Tooltip initialization
    $('[title]').tooltip();

    // Initialize popovers for additional info
    $('[data-bs-toggle="popover"]').popover();
});

// Global notification function
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}

// Print specific sections
function printSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Section</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    ${section.outerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}
</script>
@endpush

@push('styles')
<style>
/* Enhanced Info Box Styling */
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: .25rem;
    background-color: #fff;
    display: flex;
    margin-bottom: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,.15);
}

.info-box-icon {
    border-radius: .25rem 0 0 .25rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    width: 90px;
}

.info-box-content {
    padding: .75rem .75rem .75rem 0;
    margin-left: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: .8rem;
    color: #6c757d;
}

.info-box-number {
    font-weight: bold;
    font-size: 1.5rem;
}

/* Table Enhancements */
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    background-color: #343a40 !important;
    color: white !important;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

.item-description {
    line-height: 1.4;
}

.property-numbers {
    line-height: 1.4;
}

.old-property-no {
    font-weight: 500;
    font-family: 'Courier New', monospace;
}

.serial-no {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
}

.unit-value {
    font-weight: 500;
    color: #28a745;
    font-family: 'Courier New', monospace;
}

.location-text {
    font-size: 0.9em;
}

/* Info Section Styling */
.info-section {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #007bff;
}

/* Summary Section */
.summary-section {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
    border: 1px solid #e9ecef;
}

/* Filter Section */
.filter-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.export-options {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

/* Empty State */
.empty-state {
    padding: 40px 20px;
    text-align: center;
}

.empty-state i {
    opacity: 0.5;
}

/* Button Group Enhancements */
.btn-group-sm > .btn {
    padding: .25rem .5rem;
    font-size: .775rem;
    border-radius: .2rem;
}

/* Badge Enhancements */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* DataTable Customizations */
.dataTables_wrapper .dataTables_filter {
    float: right;
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_length {
    float: left;
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 8px;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    padding-top: 8px;
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Print Styles */
@media print {
    .no-print,
    .card-tools,
    .btn,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate,
    .filter-section,
    .export-options {
        display: none !important;
    }

    .card {
        border: none;
        box-shadow: none;
    }

    .table {
        font-size: 12px;
    }

    .info-box {
        break-inside: avoid;
        margin-bottom: 10px;
    }

    body {
        font-size: 12px;
        line-height: 1.3;
    }

    .card-header {
        background: none !important;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .section-title {
        border-bottom: 1px solid #000;
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .info-box {
        flex-direction: column;
        text-align: center;
    }
    
    .info-box-icon {
        width: 100%;
        border-radius: .25rem .25rem 0 0;
        padding: 20px;
    }
    
    .info-box-content {
        margin-left: 0;
        padding: 15px;
    }

    .card-tools {
        margin-top: 10px;
    }

    .card-tools .btn {
        margin-bottom: 5px;
    }

    .filter-section,
    .export-options {
        margin-bottom: 15px;
    }

    .table-responsive {
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 10px;
    }

    .info-section,
    .summary-section {
        padding: 15px;
    }

    .section-title {
        font-size: 1.1rem;
    }

    .table th,
    .table td {
        padding: 6px 8px;
        font-size: 0.8rem;
    }

    .btn-group-sm > .btn {
        padding: .2rem .4rem;
        font-size: .7rem;
    }
}

/* Loading States */
.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Focus styles for better accessibility */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: 0;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .table th {
        background-color: #000 !important;
        color: #fff !important;
    }
    
    .badge {
        border: 2px solid currentColor;
    }
    
    .info-box {
        border: 2px solid #000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .info-box,
    .table-hover tbody tr {
        transition: none;
    }
    
    .loading::after {
        animation: none;
    }
}
</style>
@endpush