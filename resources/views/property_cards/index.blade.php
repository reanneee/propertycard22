{{-- resources/views/property_cards/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Property Cards')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Property Cards</h3>
                    <div class="btn-group">
                        <a href="{{ route('property-cards.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Property Card
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('property-cards.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by description, article, or remarks..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <select name="inventory_form_id" class="form-control">
                                    <option value="">All Inventory Forms</option>
                                    @foreach(App\Models\InventoryCountForm::all() as $form)
                                        <option value="{{ $form->id }}" 
                                                {{ request('inventory_form_id') == $form->id ? 'selected' : '' }}>
                                            {{ $form->title ?? "Form #{$form->id}" }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Property Cards Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Card #</th>
                                    <th>Article/Description</th>
                                    <th>Property No.</th>
                                    <th>Quantity</th>
                                    <th>Condition</th>
                                    <th>Location</th>
                                    <th>Inventory Form</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($propertyCards as $card)
                                <tr>
                                    <td>
                                        <strong>PC-{{ str_pad($card->property_card_id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $card->receivedEquipmentItem->receivedEquipmentDescription->description ?? 'N/A' }}</strong>
                                        @if($card->article)
                                            <br><small class="text-muted">{{ $card->article }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $card->receivedEquipmentItem->property_no ?? 'N/A' }}
                                    </td>
                                    <td class="text-center">{{ $card->qty_physical }}</td>
                                    <td class="text-center">
                                        @switch(strtolower($card->condition))
                                            @case('good')
                                            @case('excellent')
                                                <span class="badge badge-success">{{ ucfirst($card->condition) }}</span>
                                                @break
                                            @case('fair')
                                            @case('average')
                                                <span class="badge badge-warning">{{ ucfirst($card->condition) }}</span>
                                                @break
                                            @case('poor')
                                            @case('bad')
                                                <span class="badge badge-danger">{{ ucfirst($card->condition) }}</span>
                                                @break
                                            @default
                                                <span class="badge badge-info">{{ ucfirst($card->condition) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ $card->location->building_name ?? 'N/A' }}
                                        @if($card->location && $card->location->office_name)
                                            - {{ $card->location->office_name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($card->inventoryCountForm)
                                            <a href="{{ route('inventory-count-form.show', $card->inventoryCountForm->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Form #{{ $card->inventoryCountForm->id }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('property-cards.show', $card->property_card_id) }}" 
                                               class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('property-cards.edit', $card->property_card_id) }}" 
                                               class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('property-cards.destroy', $card->property_card_id) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this property card?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No property cards found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($propertyCards->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $propertyCards->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection