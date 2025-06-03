{{-- resources/views/inventory_count_form/item_details.blade.php --}}
@extends('layouts.app')

@section('title', 'Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('inventory-count-form.index') }}">Inventory Count Forms</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('inventory-count-form.show', request()->route('inventoryFormId')) }}">
                            Form Details
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Item Details</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>Property Card Details
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory-count-form.show', request()->route('inventoryFormId')) }}"
                            class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Form
                        </a>
                        <a href="{{ route('inventory-count-form.edit-item-details', ['inventoryFormId' => request()->route('inventoryFormId'), 'itemId' => request()->route('itemId')]) }}"
                            class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                   
                        <!-- New PDF Download Button -->
                        <a href="{{ route('inventory-count-form.generate-pdf', ['inventoryFormId' => request()->route('inventoryFormId'), 'itemId' => request()->route('itemId')]) }}"
                            class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i>Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Header Information -->
                    <div class="property-card-header">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <td class="header-field" width="50%">
                                    <strong>Entity Name:</strong>
                                    <span class="underline-field">{{ $itemDetails->entity_name ?? '_________________________' }}</span>
                                </td>
                                <!-- <td class="header-field" width="50%">
                                    <strong>Fund Cluster:</strong> 
                                    <span class="underline-field">{{ $itemDetails->fund_cluster ?? '_________________________' }}</span>
                                </td> -->
                            </tr>
                        </table>
                    </div>

                    <!-- Main Property Information -->
                    <div class="property-main-info">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <td class="main-field" width="70%">
                                    <strong>Property, Plant and Equipment:</strong><br>
                                    <span class="field-content">{{ $itemDetails->article ?? 'N/A' }}</span>
                                </td>
                                <td class="main-field" width="30%">
                                    <strong>Property Number:</strong><br>
                                    <span class="field-content property-no">{{ $itemDetails->property_no ?? 'N/A' }}</span>
                                    @if($itemDetails->new_property_no)
                                    <br><small class="text-primary">New: {{ $itemDetails->new_property_no }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="main-field">
                                    <strong>Description:</strong><br>
                                    <span class="field-content">{{ $itemDetails->description ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Transaction Table -->
                    <div class="transaction-table">
                        <table class="table table-bordered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle" width="8%">Date</th>
                                    <th rowspan="2" class="text-center align-middle" width="12%">Reference/<br>PAR No.</th>
                                    <th rowspan="2" class="text-center align-middle" width="8%">Receipt<br>Qty.</th>
                                    <th colspan="2" class="text-center" width="24%">Issue/Transfer/Disposal</th>
                                    <th rowspan="2" class="text-center align-middle" width="8%">Balance<br>Qty.</th>
                                    <th rowspan="2" class="text-center align-middle" width="12%">Amount</th>
                                    <th rowspan="2" class="text-center align-middle" width="28%">Remarks</th>
                                </tr>
                                <tr>
                                    <th class="text-center" width="8%">Qty.</th>
                                    <th class="text-center" width="16%">Office/Officer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Initial Receipt Record -->
                                <tr>
                                    <td class="text-center">
                                        {{ $itemDetails->date_acquired ? \Carbon\Carbon::parse($itemDetails->date_acquired)->format('m/d/Y') : '' }}
                                    </td>
                                    <td class="text-center">{{ $itemDetails->par_no ?? '' }}</td>
                                    <td class="text-center">{{ $itemDetails->original_quantity ?? '' }}</td>
                                    <td class="text-center">{{ $itemDetails->physical_quantity ?? $itemDetails->original_quantity ?? '' }}</td>
                                    <td class="text-center">
                                        @if($itemDetails->received_by_name)
                                        {{ $itemDetails->received_by_name }}
                                        @elseif($itemDetails->issue_transfer_disposal)
                                        {{ $itemDetails->issue_transfer_disposal }}
                                        @elseif($itemDetails->location && $itemDetails->location !== 'Not Specified')
                                        {{ $itemDetails->location }}
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $itemDetails->original_quantity ?? '' }}</td>
                                    <td class="text-center amount-cell">
                                        {{ $itemDetails->amount ? '₱' . number_format($itemDetails->amount, 2) : '' }}
                                    </td>
                                    <td>{{ $itemDetails->issue_transfer_disposal ?? 'Transferred' }}</td>
                                </tr>

                                <!-- Transfer/Issue Record (if applicable) -->


                                <!-- Empty rows for additional entries -->
                                @for($i = 0; $i < 5; $i++)
                                    <tr class="empty-row">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    </tr>
                                    @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Additional Information Footer -->
                    <div class="additional-info">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <td width="25%"><strong>Serial Number:</strong><br>{{ $itemDetails->serial_no ?? 'N/A' }}</td>
                                <td width="25%"><strong>Unit:</strong><br>{{ $itemDetails->unit ?? 'N/A' }}</td>
                                <td width="25%"><strong>Location:</strong><br>{{ $itemDetails->location ?? 'Not Specified' }}</td>
                                <td width="25%">
                                    <strong>Condition:</strong><br>
                                    @if($itemDetails->condition)
                                    <span class="badge bg-{{ $itemDetails->condition == 'Good' ? 'success' : ($itemDetails->condition == 'Fair' ? 'warning' : 'danger') }}">
                                        {{ $itemDetails->condition }}
                                    </span>
                                    @else
                                    N/A
                                    @endif
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Property Card Styling */
    .property-card-header .table,
    .property-main-info .table,
    .transaction-table .table,
    .additional-info .table {
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .property-card-header .table td,
    .property-main-info .table td,
    .additional-info .table td {
        padding: 12px 15px;
        vertical-align: top;
    }

    .header-field,
    .main-field {
        background-color: #f8f9fa;
        font-size: 0.95rem;
    }

    .underline-field {
        border-bottom: 1px solid #000;
        display: inline-block;
        min-width: 200px;
        margin-left: 10px;
    }

    .field-content {
        display: block;
        margin-top: 5px;
        font-weight: normal;
        min-height: 20px;
    }

    .property-no {
        font-family: 'Courier New', monospace;
        font-weight: 500;
        font-size: 1.1em;
    }

    /* Transaction Table Styling */
    .transaction-table .table th {
        background-color: #343a40;
        color: white;
        font-size: 0.85rem;
        padding: 8px 6px;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #000;
    }

    .transaction-table .table td {
        padding: 8px 6px;
        font-size: 0.85rem;
        border: 1px solid #000;
        vertical-align: middle;
    }

    .amount-cell {
        font-weight: 600;
        color: #28a745;
    }

    .empty-row td {
        height: 35px;
        border: 1px solid #000;
    }

    /* Additional Info Styling */
    .additional-info .table td {
        padding: 10px 12px;
        font-size: 0.9rem;
        vertical-align: top;
    }

    /* Print Styles */
    @media print {

        .card-header,
        .breadcrumb {
            display: none !important;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-dark th {
            background-color: #343a40 !important;
            color: white !important;
        }

        .header-field,
        .main-field {
            background-color: #f8f9fa !important;
        }

        .underline-field {
            border-bottom: 1px solid #000 !important;
        }

        .card {
            box-shadow: none;
            border: none;
        }

        .card-body {
            padding: 0 !important;
        }

        /* Ensure table fits on page */
        .transaction-table {
            font-size: 0.8rem;
        }

        .transaction-table .table th,
        .transaction-table .table td {
            padding: 6px 4px !important;
            font-size: 0.75rem !important;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .transaction-table {
            font-size: 0.75rem;
        }

        .transaction-table .table th,
        .transaction-table .table td {
            padding: 6px 4px;
            font-size: 0.75rem;
        }

        .underline-field {
            min-width: 150px;
        }

        .card-tools .btn {
            margin-bottom: 5px;
        }

        .card-tools {
            flex-direction: column;
            align-items: stretch;
        }
    }

    /* Table borders for official look */
    .table-bordered {
        border: 2px solid #000;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000;
    }
</style>
@endpush