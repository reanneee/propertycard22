{{-- resources/views/property_cards/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Property Card Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Property Card #PC-{{ str_pad($propertyCard->property_card_id, 6, '0', STR_PAD_LEFT) }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('property-cards.edit', $propertyCard->property_card_id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('property-cards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Property Card Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary">Equipment Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $propertyCard->receivedEquipmentItem->receivedEquipmentDescription->description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Article:</strong></td>
                                    <td>{{ $propertyCard->article ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Property Number:</strong></td>
                                    <td>{{ $propertyCard->receivedEquipmentItem->property_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Serial Number:</strong></td>
                                    <td>{{ $propertyCard->receivedEquipmentItem->serial_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit of Measurement:</strong></td>
                                    <td>{{ $propertyCard->receivedEquipmentItem->receivedEquipmentDescription->unit ?? 'pcs' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit Value:</strong></td>
                                    <td>
                                        @if($propertyCard->receivedEquipmentItem->amount)
                                            ₱{{ number_format($propertyCard->receivedEquipmentItem->amount, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date Acquired:</strong></td>
                                    <td>
                                        @if($propertyCard->receivedEquipmentItem->date_acquired)
                                            {{ \Carbon\Carbon::parse($propertyCard->receivedEquipmentItem->date_acquired)->format('F d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-primary">Property Card Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Physical Quantity:</strong></td>
                                    <td><span class="badge badge-info badge-lg">{{ $propertyCard->qty_physical }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Condition:</strong></td>
                                    <td>
                                        @switch(strtolower($propertyCard->condition))
                                            @case('good')
                                            @case('excellent')
                                                <span class="badge badge-success badge-lg">{{ ucfirst($propertyCard->condition) }}</span>
                                                @break
                                            @case('fair')
                                            @case('average')
                                                <span class="badge badge-warning badge-lg">{{ ucfirst($propertyCard->condition) }}</span>
                                                @break
                                            @case('poor')
                                            @case('bad')
                                                <span class="badge badge-danger badge-lg">{{ ucfirst($propertyCard->condition) }}</span>
                                                @break
                                            @default
                                                <span class="badge badge-info badge-lg">{{ ucfirst($propertyCard->condition) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>
                                        @if($propertyCard->location)
                                            {{ $propertyCard->location->building_name }}
                                            @if($propertyCard->location->office_name)
                                                - {{ $propertyCard->location->office_name }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Issue/Transfer/Disposal:</strong></td>
                                    <td>{{ $propertyCard->issue_transfer_disposal ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Received By:</strong></td>
                                    <td>{{ $propertyCard->received_by_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Inventory Form:</strong></td>
                                    <td>
                                        @if($propertyCard->inventoryCountForm)
                                            <a href="{{ route('inventory-count-form.show', $propertyCard->inventoryCountForm->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Form #{{ $propertyCard->inventoryCountForm->id }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $propertyCard->created_at->format('F d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $propertyCard->updated_at->format('F d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Remarks Section -->
                    @if($propertyCard->remarks)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary">Remarks</h5>
                            <div class="alert alert-info">
                                {{ $propertyCard->remarks }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Entity Information -->
                    @if($propertyCard->receivedEquipmentItem->receivedEquipmentDescription->receivedEquipment->entity ?? null)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary">Entity Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Entity:</strong></td>
                                    <td>{{ $propertyCard->receivedEquipmentItem->receivedEquipmentDescription->receivedEquipment->entity->entity_name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn-group, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .badge-lg {
        font-size: 1rem !important;
        padding: 0.5rem 1rem !important;
    }
}
</style>
@endsection