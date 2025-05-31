@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Equipment Details</h2>
            <small class="text-muted">PAR No: {{ $equipment->par_no }}</small>
        </div>
        <div>
            <a href="{{ route('received_equipment.index') }}" class="btn btn-outline-primary me-2">← Back to List</a>
        </div>
    </div>

    <!-- Main Details Table -->
    <div class="card shadow-sm mb-5">
        <div class="card-body p-4">
            <table class="table table-borderless mb-0">
                <tr>
                    <th class="text-muted" style="width: 200px;">Entity</th>
                    <td>{{ $equipment->entity->entity_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Date Acquired</th>
                    <td>{{ $equipment->date_acquired->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Total Amount</th>
                    <td><strong>₱{{ number_format($equipment->amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Inventory Form Section -->

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-clipboard-list me-2"></i>
                @if($inventoryExists)
                    Inventory Count Form
                @else
                    Create Inventory Count Form
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($inventoryExists)
                <!-- View Inventory Section -->
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('inventory.show', $existingInventory->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i>View Inventory Count Form
                        </a>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Inventory Already Exists:</strong> An inventory count form has already been created for this equipment.
                    @if($existingInventory->inventory_date)
                        <br><strong>Inventory Date:</strong> {{ $existingInventory->inventory_date->format('Y-m-d') }}
                    @endif
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Available Qty</th>
                                <th>Unit</th>
                                <th>Serial Numbers</th>
                                <th>Property Numbers</th>
                                <th>Individual Amounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipment->descriptions as $description)
                            <tr class="table-success">
                                <td>
                                    <input type="hidden" 
                                           name="selected_items[]" 
                                           value="{{ $description->description_id }}" 
                                           data-description="{{ $description->description }}"
                                           data-quantity="{{ $description->quantity }}"
                                           data-unit="{{ $description->unit }}"
                                           data-par="{{ $equipment->par_no }}">
                                    <strong>{{ $description->description }}</strong>
                                    @if($description->items->count())
                                        <br><small class="text-muted">{{ $description->items->count() }} items</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">{{ $description->quantity }}</span>
                                </td>
                                <td>{{ $description->unit }}</td>
                                <td>
                                    @if($description->items->count())
                                        <div class="small">
                                            @foreach($description->items as $item)
                                                <div class="mb-1">
                                                    <code>{{ $item->serial_no ?: 'N/A' }}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($description->items->count())
                                        <div class="small">
                                            @foreach($description->items as $item)
                                                <div class="mb-1">
                                                    <strong>{{ $item->property_no }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($description->items->count())
                                        <div class="small">
                                            @foreach($description->items as $item)
                                                <div class="mb-1">
                                                    ₱{{ number_format($item->amount, 2) }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr class="my-2">
                                        <strong class="text-success">
                                            Total: ₱{{ number_format($description->items->sum('amount'), 2) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No equipment descriptions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($equipment->descriptions->count())
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>All items selected:</strong> {{ $equipment->descriptions->count() }} item(s)
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>Total Equipment Value:</strong> ₱{{ number_format($equipment->descriptions->flatMap->items->sum('amount'), 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
             
         

            <form id="inventoryForm" action="{{ route('inventory.create') }}" method="POST">
                @csrf
                <input type="hidden" name="par_no" value="{{ $equipment->par_no }}">
                <input type="hidden" name="entity_id" value="{{ $equipment->entity->entity_id }}">
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <button type="button" id="createInventory" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i>Create Inventory Count Form
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Available Qty</th>
                                <th>Unit</th>
                                <th>Serial Numbers</th>
                                <th>Property Numbers</th>
                                <th>Individual Amounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipment->descriptions as $description)
                            <tr class="table-success">
                                <td>
                                    <input type="hidden" 
                                           name="selected_items[]" 
                                           value="{{ $description->description_id }}" 
                                           data-description="{{ $description->description }}"
                                           data-quantity="{{ $description->quantity }}"
                                           data-unit="{{ $description->unit }}"
                                           data-par="{{ $equipment->par_no }}">
                                    <strong>{{ $description->description }}</strong>
                                    @if($description->items->count())
                                        <br><small class="text-muted">{{ $description->items->count() }} items</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">{{ $description->quantity }}</span>
                                </td>
                                <td>{{ $description->unit }}</td>
                                <td>
                                    @if($description->items->count())
                                        <div class="small">
                                            @foreach($description->items as $item)
                                                <div class="mb-1">
                                                    <code>{{ $item->serial_no ?: 'N/A' }}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($description->items->count())
                                        <div class="small">
                                            @foreach($description->items as $item)
                                                <div class="mb-1">
                                                    <strong>{{ $item->property_no }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($description->items->count())
                                        <div class="small">
                                            @foreach($description->items as $item)
                                                <div class="mb-1">
                                                    ₱{{ number_format($item->amount, 2) }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr class="my-2">
                                        <strong class="text-success">
                                            Total: ₱{{ number_format($description->items->sum('amount'), 2) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No equipment descriptions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($equipment->descriptions->count())
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>All items selected:</strong> {{ $equipment->descriptions->count() }} item(s)
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>Total Equipment Value:</strong> ₱{{ number_format($equipment->descriptions->flatMap->items->sum('amount'), 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            </form>
            @endif

            
        </div>
    </div>
</div>

<!-- Quantity Modal -->
<div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="quantityModalLabel">
                    <i class="fas fa-edit me-2"></i>Set Inventory Count Quantities
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>PAR No:</strong> {{ $equipment->par_no }} - Set the quantities you want to count for each item.
                </div>
                <div id="quantityInputs"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" id="confirmCreate" class="btn btn-primary">
                    <i class="fas fa-check me-1"></i>Create Inventory Count
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createInventoryBtn = document.getElementById('createInventory');
    const quantityModal = new bootstrap.Modal(document.getElementById('quantityModal'));
    const quantityInputs = document.getElementById('quantityInputs');
    const confirmCreateBtn = document.getElementById('confirmCreate');
    const selectedItems = document.querySelectorAll('input[name="selected_items[]"]');

    // Update button text to show total count
    const totalCount = selectedItems.length;
    if (totalCount > 0) {
        createInventoryBtn.innerHTML = `<i class="fas fa-plus-circle me-1"></i>Create Inventory Count Form (${totalCount} items)`;
    }

    // Create inventory form submission
    createInventoryBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (selectedItems.length === 0) {
            alert('No items available to create an inventory count form.');
            return;
        }

        // Show entity information in the modal
        let quantityHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Creating inventory count for <strong>${selectedItems.length}</strong> item(s) from PAR No: <strong>{{ $equipment->par_no }}</strong>
                <br><strong>Entity:</strong> {{ $equipment->entity->entity_name ?? 'N/A' }}
            </div>
        `;
        
        selectedItems.forEach((item, index) => {
            const description = item.dataset.description;
            const maxQuantity = parseInt(item.dataset.quantity);
            const unit = item.dataset.unit || '';
            const descriptionId = item.value;
            
            quantityHTML += `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <h6 class="card-subtitle mb-2 text-muted">Item ${index + 1}</h6>
                                <h5 class="card-title">${description}</h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-box me-1"></i>Available: <strong>${maxQuantity} ${unit}</strong>
                                </p>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Count Quantity:</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control quantity-input" 
                                           name="quantities[${descriptionId}]"
                                           data-description-id="${descriptionId}"
                                           min="1" 
                                           max="${maxQuantity}" 
                                           value="${maxQuantity}"
                                           required>
                                    <span class="input-group-text">${unit}</span>
                                </div>
                                <small class="text-muted">Maximum: ${maxQuantity}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        quantityInputs.innerHTML = quantityHTML;
        quantityModal.show();
    });

    // Confirm create inventory
    confirmCreateBtn.addEventListener('click', function() {
        const form = document.getElementById('inventoryForm');
        const quantityInputsElements = document.querySelectorAll('.quantity-input');
        
        // Validate all inputs
        let isValid = true;
        quantityInputsElements.forEach(input => {
            if (!input.value || parseInt(input.value) < 1) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            alert('Please enter valid quantities for all items.');
            return;
        }
        
        // Remove any existing quantity hidden inputs to prevent duplicates
        const existingQuantityInputs = form.querySelectorAll('input[name^="quantities["]');
        existingQuantityInputs.forEach(input => input.remove());
        
        // Add current quantity inputs to the main form
        quantityInputsElements.forEach(input => {
            if (input.value && input.dataset.descriptionId) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `quantities[${input.dataset.descriptionId}]`;
                hiddenInput.value = parseInt(input.value);
                form.appendChild(hiddenInput);
            }
        });
        
        // Hide the modal and submit the form
        quantityModal.hide();
        
        // Show loading state
        confirmCreateBtn.disabled = true;
        confirmCreateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
        
        form.submit();
    });
});
</script>

@endsection