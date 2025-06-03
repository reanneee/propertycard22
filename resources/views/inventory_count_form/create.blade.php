@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Inventory Count Form</h1>
        <a href="{{ route('descriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Descriptions
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST" id="inventoryForm">
        @csrf

    <!-- Form Header Information -->
    <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Inventory Count Information</h5>
            </div>
            <div class="card-body">
                <!-- Title and Fund Selection Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Inventory Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="{{ old('title') }}" required 
                               placeholder="e.g., Annual Inventory Count 2025, Equipment Inventory - Office A">
                        <small class="text-muted">Enter a descriptive title for this inventory count</small>
                    </div>
                    <div class="col-md-4">
                        <label for="fund_id" class="form-label">Fund/Account Code <span class="text-danger">*</span></label>
                        <select class="form-select" id="fund_id" name="fund_id" required>
                            <option value="">Select Fund/Account</option>
                            @foreach($funds as $fund)
                            <option value="{{ $fund->id }}" {{ old('fund_id') == $fund->id ? 'selected' : '' }}>
                                {{ $fund->account_code }} - {{ $fund->account_title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Entity and Date Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="entity_id" class="form-label">Entity <span class="text-danger">*</span></label>
                        @if($selectedEntityId && $selectedEntity)
                            {{-- If entity is pre-selected from equipment, show it as read-only --}}
                            <div class="form-control bg-light">
                                <i class="fas fa-building me-2"></i>{{ $selectedEntity->entity_name }}
                                <input type="hidden" name="entity_id" value="{{ $selectedEntityId }}">
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Entity automatically selected from Equipment PAR: {{ $parNo ?? 'N/A' }}
                            </small>
                        @else
                            {{-- If no entity pre-selected, show dropdown --}}
                            <select class="form-select" id="entity_id" name="entity_id" required>
                                <option value="">Select Entity</option>
                                @foreach($entities as $entity)
                                <option value="{{ $entity->entity_id }}" {{ old('entity_id') == $entity->entity_id ? 'selected' : '' }}>
                                    {{ $entity->entity_name }}
                                </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="inventory_date" class="form-label">Inventory Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="inventory_date" name="inventory_date" 
                               value="{{ old('inventory_date', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <!-- Summary Information Row -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="total_items" class="form-label">Total Items</label>
                        <input type="text" class="form-control" id="total_items" readonly>
                    </div>
                </div>
            </div>
        </div>
{{-- Add this information section to show PAR details --}}
@if($parNo)
<div class="alert alert-info mb-4">
    <div class="row">
        <div class="col-md-8">
            <h6 class="alert-heading mb-2">
                <i class="fas fa-clipboard-list me-2"></i>Inventory Information
            </h6>
            <p class="mb-1"><strong>PAR Number:</strong> {{ $parNo }}</p>
            @if($selectedEntity)
                <p class="mb-1"><strong>Entity:</strong> {{ $selectedEntity->entity_name }}</p>
            @endif
            <p class="mb-0"><strong>Total Items:</strong> {{ $processedDescriptions->count() }} descriptions</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-primary fs-6">
                Auto-filled from Equipment
            </span>
        </div>
    </div>
</div>
@endif

{{-- Add a toggle option if you want to allow entity override --}}
@if($selectedEntityId && $selectedEntity)
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="overrideEntity">
        <label class="form-check-label text-muted" for="overrideEntity">
            <small>Change entity selection</small>
        </label>
    </div>
</div>

<div id="entityOverrideSection" style="display: none;" class="mb-4">
    <label for="entity_id_override" class="form-label">Select Different Entity <span class="text-danger">*</span></label>
    <select class="form-select" id="entity_id_override" name="entity_id_override">
        <option value="">Select Entity</option>
        @foreach($entities as $entity)
        <option value="{{ $entity->entity_id }}" {{ $entity->entity_id == $selectedEntityId ? 'selected' : '' }}>
            {{ $entity->entity_name }}
        </option>
        @endforeach
    </select>
</div>
        <!-- Selected Equipment Items -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Selected Equipment for Inventory Count</h5>
                <small class="text-muted">Complete the inventory details for each selected item</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="inventoryTable">
                        <thead class="table-light">
                            <tr>
                                <th width="7%">Article/Item</th>
                                <th width="18%">Description</th>
                                <th width="10%">Old Property No.</th>
                                <th width="10%">New Property No.</th>
                                <th width="6%">Unit</th>
                                <th width="7%">Unit Value</th>
                                <th width="6%">Qty Card</th>
                                <th width="6%">Qty Physical</th>
                                <th width="10%">Location</th>
                                <th width="7%">Condition</th>
                                <th width="13%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
  <!-- Fixed section for your Blade template -->
@php $itemIndex = 0; @endphp
@foreach($processedDescriptions as $description)
    @foreach($description->items as $item)
        @php
        $fundMatch = $fundMatches->get($item->item_id);
        $equipmentItem = $equipmentItems->firstWhere('property_no', $item->property_no);
        $linkedItem = $linkedItems->get($item->property_no);
        
        // Construct the full new property number from linked_equipment_items table
        $currentNewPropertyNo = '';
        if ($linkedItem) {
            // Use the constructed full_new_property_no from the SQL query
            $currentNewPropertyNo = $linkedItem->full_new_property_no;
        }
        @endphp
        <tr class="inventory-row" data-description-id="{{ $description->description_id }}">
            <!-- Article/Item Column -->
            <td>
                <input type="text" class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][article_item]"
                    value="{{ $fundMatch->account_title ?? 'N/A' }}" readonly>
            </td>

            <!-- Description Column -->
            <td>
                <textarea class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][description]"
                    rows="2" readonly>{{ $description->description }}</textarea>
                <input type="hidden" name="inventory_items[{{ $itemIndex }}][entity_id]" value="">
                <small class="text-muted">Selected: {{ $description->inventory_quantity }} of {{ $description->total_available }} available</small>
            </td>

            <!-- Old Property No Column -->
            <td>
                <input type="text" class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][old_property_no]"
                    value="{{ $item->property_no }}" readonly>
            </td>

            <!-- New Property No Column -->
            <td>
                <input type="text" class="form-control form-control-sm new-property-input"
                    name="inventory_items[{{ $itemIndex }}][new_property_no]"
                    value="{{ $currentNewPropertyNo }}"
                    data-old-property="{{ $item->property_no }}"
                    data-fund-account-code="{{ $fundMatch->account_code ?? '' }}"
                    data-linked-item-id="{{ $linkedItem->id ?? '' }}"
                    data-reference-mmdd="{{ $linkedItem->reference_mmdd ?? '' }}"
                    data-sequence="{{ $linkedItem->new_property_no ?? '' }}"
                    data-location-code="{{ $linkedItem->location ?? '' }}"
                    readonly>
            </td>

            <!-- Unit Column -->
            <td>
                <input type="text" class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][unit]"
                    value="{{ $description->unit }}" readonly>
            </td>

            <!-- Unit Value Column -->
            <td>
                <input type="number" class="form-control form-control-sm unit-value"
                    name="inventory_items[{{ $itemIndex }}][unit_value]"
                    value="{{ $item->unit_value ?? $description->unit_value ?? 0 }}"
                    step="0.01" min="0">
            </td>

            <!-- Qty Card Column - This represents 1 unit per individual item -->
            <td>
                <input type="number" class="form-control form-control-sm qty-card"
                    name="inventory_items[{{ $itemIndex }}][qty_card]"
                    value="1"
                    min="0" readonly>
            </td>

            <!-- Qty Physical Column - Default to 1 for physical count -->
            <td>
                <input type="number" class="form-control form-control-sm qty-physical"
                    name="inventory_items[{{ $itemIndex }}][qty_physical]"
                    value="1"
                    min="0" required>
            </td>

            <!-- Location Column -->
            <td>
    <select class="form-select form-select-sm location-select"
        name="inventory_items[{{ $itemIndex }}][location]"
        data-row-index="{{ $itemIndex }}" required>
        <option value="">Select Location</option>
        @foreach($locations as $location)
        <option value="{{ $location->building_name }} - {{ $location->office_name }}"
            data-location-id="{{ $location->id }}"
            {{ (optional($equipmentItem)->location_id == $location->id) ? 'selected' : '' }}>
            {{ $location->building_name }}
            @if($location->office_name)
            - {{ $location->office_name }}
            @endif
            @if($location->officer_name)
            ({{ $location->officer_name }})
            @endif
        </option>
        @endforeach
    </select>
    <!-- Hidden field to store the actual location ID - FIXED -->
    <input type="hidden" class="location-id-input" 
           name="inventory_items[{{ $itemIndex }}][location_id]" 
           value="{{ optional($equipmentItem)->location_id ?? '' }}"
           required>
</td>

            <!-- Condition Column -->
            <td>
                <select class="form-select form-select-sm condition-select"
                    name="inventory_items[{{ $itemIndex }}][condition]" required>
                    <option value="">Select Condition</option>
                    <option value="Serviceable" {{ ($item->condition ?? '') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                    <option value="Unserviceable" {{ ($item->condition ?? '') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                    <option value="For Repair" {{ ($item->condition ?? '') == 'For Repair' ? 'selected' : '' }}>For Repair</option>
                    <option value="For Disposal" {{ ($item->condition ?? '') == 'For Disposal' ? 'selected' : '' }}>For Disposal</option>
                    <option value="Missing" {{ ($item->condition ?? '') == 'Missing' ? 'selected' : '' }}>Missing</option>
                    <option value="Damaged" {{ ($item->condition ?? '') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                    <option value="New" {{ ($item->condition ?? '') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="Used - Good" {{ ($item->condition ?? '') == 'Used - Good' ? 'selected' : '' }}>Used - Good</option>
                    <option value="Used - Fair" {{ ($item->condition ?? '') == 'Used - Fair' ? 'selected' : '' }}>Used - Fair</option>
                    <option value="Obsolete" {{ ($item->condition ?? '') == 'Obsolete' ? 'selected' : '' }}>Obsolete</option>
                </select>
            </td>

            <!-- Remarks Column -->
            <td>
                <textarea class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][remarks]"
                    rows="2" placeholder="Enter remarks..."></textarea>
            </td>
        </tr>
        @php $itemIndex++; @endphp
    @endforeach
@endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Remarks Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Additional Information</h5>
            </div>
            <div class="card-body">
              

                <!-- Signature Fields -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="prepared_by_name" class="form-label">Prepared By (Name)</label>
                        <input type="text" class="form-control" id="prepared_by_name"
                            name="prepared_by_name" value="{{ old('prepared_by_name') }}">
                        <label for="prepared_by_position" class="form-label mt-2">Position</label>
                        <input type="text" class="form-control" id="prepared_by_position"
                            name="prepared_by_position" value="{{ old('prepared_by_position') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="reviewed_by_name" class="form-label">Reviewed By (Name)</label>
                        <input type="text" class="form-control" id="reviewed_by_name"
                            name="reviewed_by_name" value="{{ old('reviewed_by_name') }}">
                        <label for="reviewed_by_position" class="form-label mt-2">Position</label>
                        <input type="text" class="form-control" id="reviewed_by_position"
                            name="reviewed_by_position" value="{{ old('reviewed_by_position') }}">
                    </div>
                   
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Summary</h6>
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 id="summaryTotalItems" class="text-primary">{{ $itemIndex }}</h4>
                                        <small>Total Items</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 id="summaryCardQty" class="text-info">0</h4>
                                        <small>Card Quantity</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 id="summaryPhysicalQty" class="text-success">0</h4>
                                        <small>Physical Count</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 id="summaryTotalValue" class="text-danger">₱0.00</h4>
                                        <small>Total Value</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Save Inventory Count
                            </button>
                            <a href="{{ route('descriptions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .table td,
    .table th {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .form-control-sm,
    .form-select-sm {
        font-size: 0.775rem;
    }

    .inventory-row:hover {
        background-color: #f8f9fa;
    }

    .qty-mismatch {
        background-color: #fff3cd;
    }

    .qty-match {
        background-color: #d1e7dd;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overrideCheckbox = document.getElementById('overrideEntity');
    const overrideSection = document.getElementById('entityOverrideSection');
    const originalEntityInput = document.querySelector('input[name="entity_id"]');
    const overrideSelect = document.getElementById('entity_id_override');
    
    if (overrideCheckbox) {
        overrideCheckbox.addEventListener('change', function() {
            if (this.checked) {
                overrideSection.style.display = 'block';
                originalEntityInput.disabled = true;
                overrideSelect.name = 'entity_id'; // Change name to entity_id
                overrideSelect.required = true;
            } else {
                overrideSection.style.display = 'none';
                originalEntityInput.disabled = false;
                overrideSelect.name = 'entity_id_override'; // Revert name
                overrideSelect.required = false;
            }
        });
    }
});
</script>
@endif
<script>



document.addEventListener('DOMContentLoaded', function() {
    const entitySelect = document.getElementById('entity_id');
    const totalItemsInput = document.getElementById('total_items');
    const inventoryRows = document.querySelectorAll('.inventory-row');

    // Initialize location IDs on page load
    function initializeLocationIds() {
        inventoryRows.forEach(row => {
            const locationSelect = row.querySelector('.location-select');
            const locationIdInput = row.querySelector('.location-id-input');

            if (locationSelect && locationIdInput) {
                // If location is already selected, update the location_id
                if (locationSelect.value) {
                    const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                    const locationId = selectedOption.getAttribute('data-location-id');
                    locationIdInput.value = locationId || '';
                    console.log(`Row initialized: Location ID = ${locationId}`);
                }
            }
        });
    }

    // Update entity_id in hidden inputs when entity is selected
    if (entitySelect) {
        entitySelect.addEventListener('change', function() {
            const selectedEntityId = this.value;
            document.querySelectorAll('input[name*="[entity_id]"]').forEach(input => {
                input.value = selectedEntityId;
            });
        });
    }

    // Function to generate new property number format
    function generateNewPropertyNumber(oldPropertyNo, fundAccountCode, locationId, inputElement) {
        if (!fundAccountCode) {
            inputElement.value = '';
            return;
        }

        let referenceMmdd = inputElement.getAttribute('data-reference-mmdd');
        let sequence = inputElement.getAttribute('data-sequence');

        if (!referenceMmdd || !sequence) {
            const digits = fundAccountCode.substring(3, 7);
            referenceMmdd = digits.substring(0, 2) + '-' + digits.substring(2, 4);
            
            const rowIndex = inputElement.closest('tr').querySelector('.location-select').getAttribute('data-row-index');
            sequence = String(parseInt(rowIndex) + 1).padStart(4, '0');
        }

        const locationCode = locationId ? String(locationId).padStart(2, '0') : '00';
        const currentYear = new Date().getFullYear();
        const newPropertyNo = `${currentYear}-${referenceMmdd}-${sequence}-${locationCode}`;
        
        inputElement.value = newPropertyNo;
        inputElement.setAttribute('data-reference-mmdd', referenceMmdd);
        inputElement.setAttribute('data-sequence', sequence);
        inputElement.setAttribute('data-location-code', locationCode);

        if (locationId && oldPropertyNo) {
            saveOrUpdateLinkedEquipmentItem(oldPropertyNo, referenceMmdd, sequence, locationCode);
        }
    }

    // Update summary statistics
    function updateSummary() {
        let totalCardQty = 0;
        let totalPhysicalQty = 0;
        let totalValue = 0;
        let totalItems = inventoryRows.length;

        inventoryRows.forEach(row => {
            const cardQty = parseInt(row.querySelector('.qty-card').value) || 0;
            const physicalQty = parseInt(row.querySelector('.qty-physical').value) || 0;
            const unitValue = parseFloat(row.querySelector('.unit-value').value) || 0;

            totalCardQty += cardQty;
            totalPhysicalQty += physicalQty;
            totalValue += (physicalQty * unitValue);

            row.classList.remove('qty-mismatch', 'qty-match');
            if (cardQty !== physicalQty) {
                row.classList.add('qty-mismatch');
            } else {
                row.classList.add('qty-match');
            }
        });

        document.getElementById('summaryTotalItems').textContent = totalItems;
        document.getElementById('summaryCardQty').textContent = totalCardQty;
        document.getElementById('summaryPhysicalQty').textContent = totalPhysicalQty;
        document.getElementById('summaryTotalValue').textContent = '₱' + totalValue.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        if (totalItemsInput) {
            totalItemsInput.value = totalItems + ' items';
        }
    }

    // Add event listeners for each row
    inventoryRows.forEach(row => {
        const qtyPhysical = row.querySelector('.qty-physical');
        const unitValue = row.querySelector('.unit-value');
        const locationSelect = row.querySelector('.location-select');
        const locationIdInput = row.querySelector('.location-id-input');

        // Add event listeners for quantity and value changes
        [qtyPhysical, unitValue].forEach(input => {
            if (input) {
                input.addEventListener('input', updateSummary);
            }
        });

        // FIXED: Handle location change properly
        if (locationSelect && locationIdInput) {
            locationSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const locationId = selectedOption.getAttribute('data-location-id');
                
                console.log(`Location changed: ${locationId}`);
                
                // CRITICAL: Update the hidden location_id field
                locationIdInput.value = locationId || '';
                
                // Update new property number
                const newPropertyInput = row.querySelector('.new-property-input');
                const oldPropertyNo = newPropertyInput.getAttribute('data-old-property');
                const fundAccountCode = newPropertyInput.getAttribute('data-fund-account-code');

                if (oldPropertyNo && fundAccountCode && locationId) {
                    generateNewPropertyNumber(oldPropertyNo, fundAccountCode, locationId, newPropertyInput);
                }
            });
        }
    });

    // Function to save linked equipment item
    function saveOrUpdateLinkedEquipmentItem(oldPropertyNo, referenceMmdd, sequence, locationCode) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }

        fetch('/api/save-linked-equipment-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({
                    original_property_no: oldPropertyNo,
                    reference_mmdd: referenceMmdd,
                    new_property_no: sequence,
                    location: locationCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Linked equipment item saved successfully');
                } else {
                    console.error('Error saving linked equipment item:', data.message);
                }
            })
            .catch(error => {
                console.error('Error saving linked equipment item:', error);
            });
    }

    // FIXED: Form validation with better location_id checking
    const inventoryForm = document.getElementById('inventoryForm');
    if (inventoryForm) {
        inventoryForm.addEventListener('submit', function(e) {
            // Check entity selection
            if (entitySelect && !entitySelect.value) {
                e.preventDefault();
                alert('Please select an entity before submitting.');
                entitySelect.focus();
                return false;
            }

            // Check all required fields
            const requiredFields = this.querySelectorAll('[required]');
            let hasEmptyFields = false;
            let firstEmptyField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    hasEmptyFields = true;
                    field.classList.add('is-invalid');
                    if (!firstEmptyField) {
                        firstEmptyField = field;
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // CRITICAL: Validate location_id fields specifically
            let missingLocationIds = false;
            inventoryRows.forEach((row, index) => {
                const locationSelect = row.querySelector('.location-select');
                const locationIdInput = row.querySelector('.location-id-input');
                
                if (!locationSelect.value || !locationIdInput.value) {
                    missingLocationIds = true;
                    locationSelect.classList.add('is-invalid');
                    console.error(`Row ${index}: Missing location or location_id`);
                } else {
                    locationSelect.classList.remove('is-invalid');
                }
            });

            if (hasEmptyFields || missingLocationIds) {
                e.preventDefault();
                alert('Please fill in all required fields and select locations for all items.');
                if (firstEmptyField) {
                    firstEmptyField.focus();
                }
                return false;
            }

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitButton.disabled = true;

            // Confirm submission
            if (!confirm('Are you sure you want to save this inventory count? This action cannot be undone.')) {
                e.preventDefault();
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                return false;
            }

            return true;
        });
    }

    // Initialize everything when page loads
    initializeLocationIds();
    updateSummary();

    // Set initial entity_id values if entity is selected
    if (entitySelect && entitySelect.value) {
        document.querySelectorAll('input[name*="[entity_id]"]').forEach(input => {
            input.value = entitySelect.value;
        });
    }

    // Generate initial property numbers for items without them
    inventoryRows.forEach((row, index) => {
        const newPropertyInput = row.querySelector('.new-property-input');
        const oldPropertyNo = newPropertyInput.getAttribute('data-old-property');
        const fundAccountCode = newPropertyInput.getAttribute('data-fund-account-code');
        const locationSelect = row.querySelector('.location-select');
        
        if (!newPropertyInput.value && oldPropertyNo && fundAccountCode) {
            let locationId = null;
            if (locationSelect && locationSelect.value) {
                const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                locationId = selectedOption.getAttribute('data-location-id');
            }

            generateNewPropertyNumber(oldPropertyNo, fundAccountCode, locationId, newPropertyInput);
        }
    });
});
</script>
@endsection