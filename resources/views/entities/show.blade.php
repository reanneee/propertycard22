@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $entity->entity_name }} - Details</h2>
        <div>
            <a href="{{ route('entities.index') }}" class="btn btn-secondary">Back to Entities</a>
            <a href="{{ route('entities.edit', $entity->entity_id) }}" class="btn btn-primary">Edit Entity</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Entity Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Entity Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Entity ID:</strong> {{ $entity->entity_id }}</p>
                    <p><strong>Entity Name:</strong> {{ $entity->entity_name }}</p>
                    <p><strong>Branch:</strong> {{ $entity->branch->branch_name ?? 'N/A' }}</p>
                    <p><strong>Fund Cluster:</strong> {{ $entity->fundCluster->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Equipment Records:</strong> {{ $entity->receivedEquipments->count() }}</p>
                    <p><strong>Total Amount:</strong> ₱{{ number_format($entity->receivedEquipments->sum('amount'), 2) }}</p>
                    <p><strong>Total Items:</strong> 
                        {{ $entity->receivedEquipments->sum(function($equipment) {
                            return $equipment->descriptions->sum(function($description) {
                                return $description->items->count();
                            });
                        }) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Received Equipment Records -->
    <div class="card">
        <div class="card-header">
            <h5>Received Equipment Records</h5>
        </div>
        <div class="card-body">
            @if($entity->receivedEquipments->count() > 0)
                @foreach($entity->receivedEquipments as $equipment)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-0">Equipment Record #{{ $equipment->equipment_id }}</h6>
                                    <small class="text-muted">PAR No: {{ $equipment->par_no ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <strong>₱{{ number_format($equipment->amount ?? 0, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Equipment Details -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <p><strong>Date Acquired:</strong> {{ $equipment->date_acquired ? $equipment->date_acquired->format('M d, Y') : 'N/A' }}</p>
                                    <p><strong>Receipt Date:</strong> {{ $equipment->receipt_date ? $equipment->receipt_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Received By:</strong> {{ $equipment->received_by_name ?? 'N/A' }}</p>
                                    <p><strong>Designation:</strong> {{ $equipment->received_by_designation ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Verified By:</strong> {{ $equipment->verified_by_name ?? 'N/A' }}</p>
                                    <p><strong>Designation:</strong> {{ $equipment->verified_by_designation ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Equipment Descriptions -->
                            @if($equipment->descriptions->count() > 0)
                                <h6>Equipment Descriptions & Items:</h6>
                                @foreach($equipment->descriptions as $description)
                                    <div class="border p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6>{{ $description->description }}</h6>
                                                <p class="text-muted mb-2">
                                                    Quantity: {{ $description->quantity }} {{ $description->unit ?? '' }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <span class="badge badge-info">{{ $description->items->count() }} Items</span>
                                            </div>
                                        </div>

                                        @if($description->items->count() > 0)
                                            <div class="table-responsive mt-3">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Property No.</th>
                                                            <th>Serial No.</th>
                                                            <th>Date Acquired</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($description->items as $item)
                                                            <tr>
                                                                <td>{{ $item->property_no }}</td>
                                                                <td>{{ $item->serial_no ?? 'N/A' }}</td>
                                                                <td>{{ $item->date_acquired ? $item->date_acquired->format('M d, Y') : 'N/A' }}</td>
                                                                <td>₱{{ number_format($item->amount, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted">No items found for this description.</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No descriptions found for this equipment record.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    No equipment records found for this entity.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection