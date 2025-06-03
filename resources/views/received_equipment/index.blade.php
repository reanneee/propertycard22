@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Received Equipment List</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>PAR No</th>
                    <th>Entity</th>
                    <th>Date Acquired</th>
                    <th>Amount</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Updated By</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipments as $equipment)
                <tr>
                    <td>{{ $equipment->par_no }}</td>
                    <td>{{ $equipment->entity->entity_name ?? 'N/A' }}</td>
                    <td>{{ $equipment->date_acquired->format('Y-m-d') }}</td>
                    <td>₱{{ number_format($equipment->amount, 2) }}</td>
                    <td>
                        <div class="">
                            {{ $equipment->createdBy->name ?? 'System' }}
                        </div>
                    </td>
                    <td>
                        <div >
                            {{ $equipment->created_at->format('M d, Y') }}<br>
                            <span >{{ $equipment->created_at->format('h:i A') }}</span>
                        </div>
                    </td>
                    <td>
                        <div >
                            {{ $equipment->updatedBy->name ?? 'System' }}
                        </div>
                    </td>
                    <td>
                        <div>
                            {{ $equipment->updated_at->format('M d, Y') }}<br>
                            <span>{{ $equipment->updated_at->format('h:i A') }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center flex-wrap gap-1">
                            <a href="{{ route('received_equipment.generate_pdf', $equipment->equipment_id) }}" 
                               class="btn btn-sm btn-outline-danger" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <a href="{{ route('received_equipment.show', $equipment->equipment_id) }}" 
                               class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('received_equipment.edit', $equipment->equipment_id) }}" 
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('received_equipment.destroy', $equipment->equipment_id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this equipment record?');" 
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-muted">No equipment found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@push('styles')
<style>
    /* Ensure table headers and cells have consistent styling */
    .table th, .table td {
        white-space: nowrap;
        vertical-align: middle;
        color: #000 !important; /* Force black text */
    }

    /* Override Bootstrap text utility classes */
    .text-primary,
    .text-warning,
    .text-info,
    .text-muted {
        color: #000 !important;
    }

    .fw-bold {
        font-weight: 600 !important;
    }
</style>
@endpush



@endsection