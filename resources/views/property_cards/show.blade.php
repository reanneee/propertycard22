@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Property Card Details</h3>
                    <div>
                        <a href="{{ route('property_cards.print', $groupedData->description_id) }}" 
                           class="btn btn-secondary" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="{{ route('property_cards.pdf', $groupedData->description_id) }}" 
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="{{ route('property_cards.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Property Card Header Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="text-primary">{{ $groupedData->entity_name }}</h5>
                                            <p class="mb-1"><strong>PAR No:</strong> {{ $groupedData->par }}</p>
                                            <p class="mb-1"><strong>Description:</strong> {{ $groupedData->description }}</p>
                                            <p class="mb-0"><strong>Property Numbers:</strong> 
                                                <span class="badge bg-info">{{ $groupedData->par_no }} </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Date Acquired:</strong> {{ \Carbon\Carbon::parse($groupedData->date_acquired)->format('M d, Y') }}</p>
                                            <p class="mb-1"><strong>Amount:</strong> ₱{{ number_format($groupedData->amount, 2) }}</p>
                                            <p class="mb-1"><strong>Receipt Quantity:</strong> {{ $groupedData->receipt_quantity }}</p>
                                            <p class="mb-0"><strong>Total Physical Qty:</strong> 
                                                <span class="badge bg-success">{{ $groupedData->total_qty_physical }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Building:</strong> {{ $groupedData->building_name }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Office:</strong> {{ $groupedData->office_name }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Officer:</strong> {{ $groupedData->officer_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Cards Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Property No</th>
                                    <th>Article</th>
                                    <th>Physical Qty</th>
                                    <th>Condition</th>
                                    <th>Balance</th>
                                    <th>Received By</th>
                                    <th>Issue/Transfer/Disposal</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($propertyCards as $card)
                                <tr>
                                    <td>{{ $card->property_number }}</td>
                                    <td>{{ $card->article }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $card->qty_physical }}</span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($card->condition == 'New') bg-success
                                            @elseif($card->condition == 'Good') bg-info
                                            @elseif($card->condition == 'Fair') bg-warning
                                            @elseif($card->condition == 'Poor') bg-secondary
                                            @elseif($card->condition == 'For Repair') bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $card->condition }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($card->balance ?? 0, 2) }}</td>
                                    <td>{{ $card->received_by_name ?? '-' }}</td>
                                    <td>
                                        @if($card->issue_transfer_disposal)
                                            <small>{{ Str::limit($card->issue_transfer_disposal, 50) }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($card->remarks)
                                            <small>{{ Str::limit($card->remarks, 50) }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('property_cards.edit', $card->property_card_id) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('property_cards.destroy', $card->property_card_id) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this property card?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($propertyCards->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            No property cards found for this description.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection