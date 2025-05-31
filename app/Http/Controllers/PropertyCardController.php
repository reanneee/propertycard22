<?php

namespace App\Http\Controllers;

use App\Models\InventoryCountForm;
use App\Models\PropertyCard;
use App\Models\ReceivedEquipmentItem;
use App\Models\ReceivedEquipmentDescription;
use App\Models\Location;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PropertyCardController extends Controller
{
    public function index(Request $request)
    {
        $propertyCards = PropertyCard::with([
            'receivedEquipmentItem.receivedEquipmentDescription',
            'location',
            'inventoryCountForm'
        ])
        ->when($request->inventory_form_id, function($query, $formId) {
            return $query->where('inventory_count_form_id', $formId);
        })
        ->when($request->search, function($query, $search) {
            return $query->whereHas('receivedEquipmentItem.receivedEquipmentDescription', function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            })
            ->orWhere('article', 'like', "%{$search}%")
            ->orWhere('remarks', 'like', "%{$search}%");
        })
        ->paginate(20);

        return view('property_cards.index', compact('propertyCards'));
    }

    public function create(Request $request)
    {
        $inventoryFormId = $request->get('inventory_form_id');
        $receivedEquipmentItemId = $request->get('received_equipment_item_id');
        
        // Get available inventory items that don't have property cards yet
        $availableItems = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->leftJoin('property_cards as pc', 'rei.item_id', '=', 'pc.received_equipment_item_id')
            ->select('rei.*', 'red.description')
            ->whereNull('pc.property_card_id')
            ->when($receivedEquipmentItemId, function($query, $itemId) {
                return $query->where('rei.item_id', $itemId);
            })
            ->get();

        $locations = Location::all();
        $inventoryForms = InventoryCountForm::all();

        return view('property_cards.create', compact(
            'availableItems', 
            'locations', 
            'inventoryForms', 
            'inventoryFormId',
            'receivedEquipmentItemId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_count_form_id' => 'nullable|exists:inventory_count_form,id',
            'received_equipment_item_id' => 'required|exists:received_equipment_item,item_id',
            'qty_physical' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'issue_transfer_disposal' => 'nullable|string|max:255',
            'received_by_name' => 'nullable|string|max:255',
            'article' => 'nullable|string|max:255',
            'locations_id' => 'required|exists:locations,id'
        ]);

        // Check if property card already exists for this item
        $existingCard = PropertyCard::where('received_equipment_item_id', $validated['received_equipment_item_id'])->first();
        
        if ($existingCard) {
            return back()->withErrors(['received_equipment_item_id' => 'Property card already exists for this item.']);
        }

        $propertyCard = PropertyCard::create($validated);

        return redirect()->route('property-cards.show', $propertyCard->property_card_id)
            ->with('success', 'Property card created successfully.');
    }

    public function show($id)
    {
        $propertyCard = PropertyCard::with([
            'receivedEquipmentItem.receivedEquipmentDescription.receivedEquipment.entity',
            'location',
            'inventoryCountForm'
        ])->findOrFail($id);

        return view('property_cards.show', compact('propertyCard'));
    }

    public function edit($id)
    {
        $propertyCard = PropertyCard::findOrFail($id);
        $locations = Location::all();
        $inventoryForms = InventoryCountForm::all();

        return view('property_cards.edit', compact('propertyCard', 'locations', 'inventoryForms'));
    }

    public function update(Request $request, $id)
    {
        $propertyCard = PropertyCard::findOrFail($id);
        
        $validated = $request->validate([
            'inventory_count_form_id' => 'nullable|exists:inventory_count_form,id',
            'qty_physical' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'issue_transfer_disposal' => 'nullable|string|max:255',
            'received_by_name' => 'nullable|string|max:255',
            'article' => 'nullable|string|max:255',
            'locations_id' => 'required|exists:locations,id'
        ]);

        $propertyCard->update($validated);

        return redirect()->route('property-cards.show', $propertyCard->property_card_id)
            ->with('success', 'Property card updated successfully.');
    }

    public function destroy($id)
    {
        $propertyCard = PropertyCard::findOrFail($id);
        $propertyCard->delete();

        return redirect()->route('property-cards.index')
            ->with('success', 'Property card deleted successfully.');
    }

    // Create property card from inventory item
    public function createFromInventoryItem($inventoryFormId, $itemId)
    {
        // Get the inventory item details
        $inventoryItem = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->select('rei.*', 'red.description', 'red.unit')
            ->where('rei.item_id', $itemId)
            ->first();

        if (!$inventoryItem) {
            abort(404, 'Inventory item not found');
        }

        // Check if property card already exists
        $existingCard = PropertyCard::where('received_equipment_item_id', $itemId)->first();
        if ($existingCard) {
            return redirect()->route('property-cards.show', $existingCard->property_card_id)
                ->with('info', 'Property card already exists for this item.');
        }

        $locations = Location::all();

        return view('property_cards.create_from_inventory', compact(
            'inventoryItem', 
            'inventoryFormId', 
            'locations'
        ));
    }

    // Bulk create property cards for inventory form
    public function bulkCreateForInventory($inventoryFormId)
    {
        $inventoryForm = InventoryCountForm::findOrFail($inventoryFormId);
        
        // Get all items without property cards for this entity
        $itemsWithoutCards = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->leftJoin('property_cards as pc', 'rei.item_id', '=', 'pc.received_equipment_item_id')
            ->select('rei.*', 'red.description', 'red.unit')
            ->where('re.entity_id', $inventoryForm->entity_id)
            ->whereNull('pc.property_card_id')
            ->get();

        $locations = Location::all();

        return view('property_cards.bulk_create', compact(
            'inventoryForm', 
            'itemsWithoutCards', 
            'locations'
        ));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'inventory_count_form_id' => 'required|exists:inventory_count_form,id',
            'items' => 'required|array',
            'items.*.received_equipment_item_id' => 'required|exists:received_equipment_item,item_id',
            'items.*.qty_physical' => 'required|integer|min:0',
            'items.*.condition' => 'required|string|max:255',
            'items.*.locations_id' => 'required|exists:locations,id',
            'items.*.article' => 'nullable|string|max:255',
            'items.*.remarks' => 'nullable|string',
        ]);

        $createdCards = 0;
        $errors = [];

        foreach ($validated['items'] as $itemData) {
            // Check if property card already exists
            $existingCard = PropertyCard::where('received_equipment_item_id', $itemData['received_equipment_item_id'])->first();
            
            if (!$existingCard) {
                PropertyCard::create([
                    'inventory_count_form_id' => $validated['inventory_count_form_id'],
                    'received_equipment_item_id' => $itemData['received_equipment_item_id'],
                    'qty_physical' => $itemData['qty_physical'],
                    'condition' => $itemData['condition'],
                    'locations_id' => $itemData['locations_id'],
                    'article' => $itemData['article'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
                $createdCards++;
            } else {
                $errors[] = "Property card already exists for item ID: {$itemData['received_equipment_item_id']}";
            }
        }

        $message = "Created {$createdCards} property cards successfully.";
        if (!empty($errors)) {
            $message .= " " . implode(' ', $errors);
        }

        return redirect()->route('inventory-count-form.show', $validated['inventory_count_form_id'])
            ->with('success', $message);
    }
}