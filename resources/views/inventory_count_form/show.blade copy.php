@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Inventory Report</h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary"   href="{{ route('inventory.print', $inventoryForm->id) }}" class="btn btn-success">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <a href="{{ route('inventory.report', $inventoryForm->id) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Report Title and Fund Information -->
                    <div class="text-center mb-4">
                        @if($inventoryForm->title)
                            <h2 class="font-weight-bold text-primary mb-3">{{ $inventoryForm->title }}</h2>
                        @else
                            <h2 class="font-weight-bold text-primary mb-3">INVENTORY REPORT</h2>
                        @endif
                        
                        @if($inventoryForm->fund_account_code)
                            <div class="alert alert-info">
                                <h4 class="mb-0">
                                    <i class="fas fa-money-bill-wave"></i> 
                                    Fund Account Code: <strong>{{ $inventoryForm->fund_account_code }}</strong>
                                </h4>
                            </div>
                        @endif
                    </div>

                    <!-- Header Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Entity:</strong></td>
                                    <td>{{ $inventoryForm->entity_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td>{{ $inventoryForm->branch_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fund Cluster:</strong></td>
                                    <td>{{ $inventoryForm->fund_cluster_name }}</td>
                                </tr>
                                @if($inventoryForm->fund_account_code)
                                <tr>
                                    <td><strong>Fund Account Code:</strong></td>
                                    <td><span class="badge badge-primary">{{ $inventoryForm->fund_account_code }}</span></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Inventory Date:</strong></td>
                                    <td>{{ $inventoryForm->inventory_date ? \Carbon\Carbon::parse($inventoryForm->inventory_date)->format('F d, Y') : 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Prepared By:</strong></td>
                                    <td>{{ $inventoryForm->prepared_by_name ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Position:</strong></td>
                                    <td>{{ $inventoryForm->prepared_by_position ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reviewed By:</strong></td>
                                    <td>{{ $inventoryForm->reviewed_by_name ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Position:</strong></td>
                                    <td>{{ $inventoryForm->reviewed_by_position ?? 'Not Set' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Inventory Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">#</th>
                                    <th rowspan="2" class="text-center align-middle">Article/Item Description</th>
                                    <th colspan="2" class="text-center">Property No.</th>
                                    <th rowspan="2" class="text-center align-middle">Unit of Measurement</th>
                                    <th rowspan="2" class="text-center align-middle">Unit Value</th>
                                    <th colspan="2" class="text-center">Quantity</th>
                                    <th rowspan="2" class="text-center align-middle">Location/Whereabouts</th>
                                    <th rowspan="2" class="text-center align-middle">Condition</th>
                                    <th rowspan="2" class="text-center align-middle">Remarks</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Old Property No.</th>
                                    <th class="text-center">Assigned New Property No.</th>
                                    <th class="text-center">Per Property Card</th>
                                    <th class="text-center">Per Physical Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryItems as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->article_description }}</strong>
                                        @if($item->article && $item->article != $item->article_description)
                                            <br><small class="text-muted">{{ $item->article }}</small>
                                        @endif
                                        @if($item->serial_no)
                                            <br><small class="text-info">S/N: {{ $item->serial_no }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->old_property_no }}</td>
                                    <td class="text-center">
                                        @if($item->new_property_no)
                                            <span class="badge badge-success">{{ $item->new_property_no }}</span>
                                        @else
                                            <span class="badge badge-warning">To Be Assigned</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->unit ?? 'pcs' }}</td>
                                    <td class="text-right">
                                        @if($item->unit_value)
                                            ₱{{ number_format($item->unit_value, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity_per_property_card ?? 0 }}</td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm text-center" 
                                               value="{{ $item->quantity_per_physical_count ?? 0 }}" 
                                               min="0" style="width: 80px; margin: 0 auto;">
                                    </td>
                                    <td>{{ $item->location_whereabouts ?? 'Not Specified' }}</td>
                                    <td class="text-center">
                                        @if($item->condition)
                                            @switch(strtolower($item->condition))
                                                @case('good')
                                                @case('excellent')
                                                    <span class="badge badge-success">{{ ucfirst($item->condition) }}</span>
                                                    @break
                                                @case('fair')
                                                @case('average')
                                                    <span class="badge badge-warning">{{ ucfirst($item->condition) }}</span>
                                                    @break
                                                @case('poor')
                                                @case('bad')
                                                    <span class="badge badge-danger">{{ ucfirst($item->condition) }}</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-info">{{ ucfirst($item->condition) }}</span>
                                            @endswitch
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->remarks ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No inventory items found for this entity.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Section -->
                    @if($inventoryItems->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Inventory Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Total Items:</strong></td>
                                            <td>{{ $inventoryItems->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Items with New Property No.:</strong></td>
                                            <td>{{ $inventoryItems->whereNotNull('new_property_no')->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Items Pending Assignment:</strong></td>
                                            <td>{{ $inventoryItems->whereNull('new_property_no')->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Estimated Value:</strong></td>
                                            <td>₱{{ number_format($inventoryItems->sum(function($item) { 
                                                return ($item->unit_value ?? 0) * ($item->quantity_per_property_card ?? 1); 
                                            }), 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Condition Distribution</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $conditions = $inventoryItems->groupBy('condition');
                                    @endphp
                                    @foreach($conditions as $condition => $items)
                                        @if($condition)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ ucfirst($condition) }}:</span>
                                            <span class="badge badge-secondary">{{ $items->count() }}</span>
                                        </div>
                                        @endif
                                    @endforeach
                                    @if($inventoryItems->whereNull('condition')->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Not Set:</span>
                                        <span class="badge badge-secondary">{{ $inventoryItems->whereNull('condition')->count() }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Signature Section -->
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="text-center">
                                <hr style="width: 200px; border: 1px solid #000;">
                                <p><strong>{{ $inventoryForm->prepared_by_name ?? 'Name' }}</strong></p>
                                <p>{{ $inventoryForm->prepared_by_position ?? 'Position' }}</p>
                                <p><small>Prepared By</small></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <hr style="width: 200px; border: 1px solid #000;">
                                <p><strong>{{ $inventoryForm->reviewed_by_name ?? 'Name' }}</strong></p>
                                <p>{{ $inventoryForm->reviewed_by_position ?? 'Position' }}</p>
                                <p><small>Reviewed By</small></p>
                            </div>
                        </div>
                    </div>
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
    
    .table {
        font-size: 10px;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add functionality for physical count inputs
    const physicalCountInputs = document.querySelectorAll('input[type="number"]');
    
    physicalCountInputs.forEach(input => {
        input.addEventListener('change', function() {
            // You can add AJAX functionality here to save changes
            console.log('Physical count updated:', this.value);
        });
    });
});
</script>
@endsection