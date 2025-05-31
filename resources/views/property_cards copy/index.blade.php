@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Property Cards Management</h3>
                    <!-- <a href="{{ route('property_cards.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Property Card
                    </a> -->
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="propertyCardsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>PAR No.</th>
                                    <th>Entity</th>
                                    <th>Description</th>
                                    <th>Property Numbers</th>
                                    <th>Date Acquired</th>
                                    <th>Amount</th>
                                    <th>Location</th>
                                    <th>Total Qty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedPropertyCards as $card)
                                <tr>
                                    <td>{{ $card->par }}</td>
                                    <td>{{ $card->entity_name }}</td>
                                    <td>{{ $card->description }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $card->par_no }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($card->date_acquired)->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($card->amount, 2) }}</td>
                                    <td>
                                        <small>
                                            <strong>{{ $card->building_name }}</strong><br>
                                            {{ $card->office_name }}<br>
                                            <em>{{ $card->officer_name }}</em>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $card->total_qty_physical }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('property_cards.show', $card->description_id) }}" 
                                               class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                      
                                            <a href="{{ route('property_cards.print', $card->description_id) }}" 
                                               class="btn btn-secondary btn-sm" title="Print" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="{{ route('property_cards.pdf', $card->description_id) }}" 
                                               class="btn btn-danger btn-sm" title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                         
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No property cards found.</td>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#propertyCardsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>
@endpush
@endsection