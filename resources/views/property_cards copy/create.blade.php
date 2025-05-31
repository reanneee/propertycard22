@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Property Card</h3>
                    <a href="{{ route('property_cards.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                
                <form action="{{ route('property_cards.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="received_equipment_item_id" class="form-label">Equipment Item <span class="text-danger">*</span></label>
                                    <select name="received_equipment_item_id" id="received_equipment_item_id" class="form-select @error('received_equipment_item_id') is-invalid @enderror" required>
                                        <option value="">Select Equipment Item</option>
                                        @foreach($receivedEquipmentItems as $item)
                                            <option value="{{ $item->item_id }}" 
                                                    data-description="{{ $item->receivedEquipmentDescription->description ?? '' }}"
                                                    data-property-no="{{ $item->property_no }}"
                                                    data-entity="{{ $item->receivedEquipmentDescription->receivedEquipment->entity->entity_name ?? '' }}"
                                                    data-amount="{{ $item->receivedEquipmentDescription->receivedEquipment->amount ?? 0 }}"
                                                    data-date-acquired="{{ $item->receivedEquipmentDescription->receivedEquipment->date_acquired ?? '' }}"
                                                    {{ old('received_equipment_item_id') == $item->item_id ? 'selected' : '' }}>
                                                {{ $item->property_no }} - {{ $item->receivedEquipmentDescription->description ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('received_equipment_item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="locations_id" class="form-label">Location <span class="text-danger">*</span></label>
                                    <select name="locations_id" id="locations_id" class="form-select @error('locations_id') is-invalid @enderror" required>
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('locations_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->building_name }} - {{ $location->office_name }} ({{ $location->officer_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('locations_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Details Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Equipment Details</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Entity:</strong>
                                                <div id="display-entity">Select equipment item first</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Property No:</strong>
                                                <div id="display-property-no">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Amount:</strong>
                                                <div id="display-amount">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Date Acquired:</strong>
                                                <div id="display-date-acquired">-</div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <strong>Description:</strong>
                                                <div id="display-description">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="article" class="form-label">Article <span class="text-danger">*</span></label>
                                    <input type="text" name="article" id="article" class="form-control @error('article') is-invalid @enderror" 
                                           value="{{ old('article') }}" required>
                                    @error('article')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="qty_physical" class="form-label">Physical Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="qty_physical" id="qty_physical" class="form-control @error('qty_physical') is-invalid @enderror" 
                                           value="{{ old('qty_physical', 1) }}" min="0" required>
                                    @error('qty_physical')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="balance" class="form-label">Balance</label>
                                    <input type="number" name="balance" id="balance" class="form-control @error('balance') is-invalid @enderror" 
                                           value="{{ old('balance') }}" step="0.01" min="0">
                                    @error('balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="condition" class="form-label">Condition <span class="text-danger">*</span></label>
                                    <select name="condition" id="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                        <option value="">Select Condition</option>
                                        <option value="New" {{ old('condition') == 'New' ? 'selected' : '' }}>New</option>
                                        <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                                        <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="Poor" {{ old('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                                        <option value="For Repair" {{ old('condition') == 'For Repair' ? 'selected' : '' }}>For Repair</option>
                                        <option value="Condemned" {{ old('condition') == 'Condemned' ? 'selected' : '' }}>Condemned</option>
                                    </select>
                                    @error('condition')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="received_by_name" class="form-label">Received By</label>
                                    <input type="text" name="received_by_name" id="received_by_name" class="form-control @error('received_by_name') is-invalid @enderror" 
                                           value="{{ old('received_by_name') }}">
                                    @error('received_by_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="issue_transfer_disposal" class="form-label">Issue/Transfer/Disposal</label>
                                    <textarea name="issue_transfer_disposal" id="issue_transfer_disposal" class="form-control @error('issue_transfer_disposal') is-invalid @enderror" 
                                              rows="3">{{ old('issue_transfer_disposal') }}</textarea>
                                    @error('issue_transfer_disposal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                              rows="3">{{ old('remarks') }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Property Card
                        </button>
                        <a href="{{ route('property_cards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#received_equipment_item_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        
        if (selectedOption.val()) {
            $('#display-entity').text(selectedOption.data('entity') || 'N/A');
            $('#display-property-no').text(selectedOption.data('property-no') || 'N/A');
            $('#display-amount').text('₱' + parseFloat(selectedOption.data('amount') || 0).toLocaleString('en-US', {minimumFractionDigits: 2}));
            $('#display-date-acquired').text(selectedOption.data('date-acquired') ? new Date(selectedOption.data('date-acquired')).toLocaleDateString() : 'N/A');
            $('#display-description').text(selectedOption.data('description') || 'N/A');
        } else {
            $('#display-entity, #display-property-no, #display-amount, #display-date-acquired, #display-description').text('-');
        }
    });

    // Trigger change event on page load if there's a selected value
    if ($('#received_equipment_item_id').val()) {
        $('#received_equipment_item_id').trigger('change');
    }
});
</script>
@endpush
@endsection