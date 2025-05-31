
{{-- resources/views/property_cards/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Property Card')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Property Card</h3>
                </div>
                
                <form method="POST" action="{{ route('property-cards.store') }}">
                    @csrf
                    <div class="card-body">
                        <!-- Inventory Form Selection -->
                        <div class="form-group row">
                            <label for="inventory_count_form_id" class="col-sm-2 col-form-label">Inventory Form</label>
                            <div class="col-sm-10">
                                <select name="inventory_count_form_id" id="inventory_count_form_id" class="form-control">
                                    <option value="">Select Inventory Form (Optional)</option>
                                    @foreach($inventoryForms as $form)
                                        <option value="{{ $form->id }}" 
                                                {{ old('inventory_count_form_id', $inventoryFormId) == $form->id ? 'selected' : '' }}>
                                            {{ $form->title ?? "Form #{$form->id}" }} - {{ $form->inventory_date }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('inventory_count_form_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Equipment Item Selection -->
                        <div class="form-group row">
                            <label for="received_equipment_item_id" class="col-sm-2 col-form-label">Equipment Item *</label>
                            <div class="col-sm-10">
                                <select name="received_equipment_item_id" id="received_equipment_item_id" class="form-control" required>
                                    <option value="">Select Equipment Item</option>
                                    @foreach($availableItems as $item)
                                        <option value="{{ $item->item_id }}" 
                                                {{ old('received_equipment_item_id', $receivedEquipmentItemId) == $item->item_id ? 'selected' : '' }}>
                                            {{ $item->description }} - {{ $item->property_no }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('received_equipment_item_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Article -->
                        <div class="form-group row">
                            <label for="article" class="col-sm-2 col-form-label">Article</label>
                            <div class="col-sm-10">
                                <input type="text" name="article" id="article" class="form-control" 
                                       value="{{ old('article') }}" placeholder="Specific article name or model">
                                @error('article')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Physical Quantity -->
                        <div class="form-group row">
                            <label for="qty_physical" class="col-sm-2 col-form-label">Physical Quantity *</label>
                            <div class="col-sm-10">
                                <input type="number" name="qty_physical" id="qty_physical" class="form-control" 
                                       value="{{ old('qty_physical', 1) }}" min="0" required>
                                @error('qty_physical')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="form-group row">
                            <label for="condition" class="col-sm-2 col-form-label">Condition *</label>
                            <div class="col-sm-10">
                                <select name="condition" id="condition" class="form-control" required>
                                    <option value="">Select Condition</option>
                                    <option value="Excellent" {{ old('condition') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="Poor" {{ old('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                                    <option value="Serviceable" {{ old('condition') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                                    <option value="Unserviceable" {{ old('condition') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                                </select>
                                @error('condition')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="form-group row">
                            <label for="locations_id" class="col-sm-2 col-form-label">Location *</label>
                            <div class="col-sm-10">
                                <select name="locations_id" id="locations_id" class="form-control" required>
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('locations_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->building_name }}
                                            @if($location->office_name)
                                                - {{ $location->office_name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('locations_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Issue/Transfer/Disposal -->
                        <div class="form-group row">
                            <label for="issue_transfer_disposal" class="col-sm-2 col-form-label">Issue/Transfer/Disposal</label>
                            <div class="col-sm-10">
                                <input type="text" name="issue_transfer_disposal" id="issue_transfer_disposal" class="form-control" 
                                       value="{{ old('issue_transfer_disposal') }}" placeholder="Issue, transfer or disposal information">
                                @error('issue_transfer_disposal')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Received By -->
                        <div class="form-group row">
                            <label for="received_by_name" class="col-sm-2 col-form-label">Received By</label>
                            <div class="col-sm-10">
                                <input type="text" name="received_by_name" id="received_by_name" class="form-control" 
                                       value="{{ old('received_by_name') }}" placeholder="Name of person who received the item">
                                @error('received_by_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="form-group row">
                            <label for="remarks" class="col-sm-2 col-form-label">Remarks</label>
                            <div class="col-sm-10">
                                <textarea name="remarks" id="remarks" class="form-control" rows="3" 
                                          placeholder="Additional notes or remarks">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Property Card
                                </button>
                                <a href="{{ route('property-cards.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection