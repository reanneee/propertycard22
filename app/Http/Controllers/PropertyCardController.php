<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\PropertyCard;
use App\Models\InventoryCountForm;
use App\Models\Location;
use App\Models\ReceivedEquipmentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyCardController extends Controller
{
    public function showItemDetails($inventoryFormId, $itemId)
    {
        // Get detailed information for a specific item
        $itemDetails = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
            ->leftJoin('property_cards as pc', function($join) use ($inventoryFormId) {
                $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                     ->where('pc.inventory_count_form_id', '=', $inventoryFormId);
            })
            ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
            ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
            ->select(
                // Entity Information
                'e.entity_name',
                
                // Property Information
                'rei.property_no',
                DB::raw("CASE 
                    WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
                    THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
                    ELSE NULL 
                END as new_property_no"),
                
                // Description and Details
                'red.description',
                'rei.date_acquired',
                're.par_no',
                'rei.serial_no',
                'red.unit',
                
                // Quantity Information
                'red.quantity as original_quantity',
                'pc.qty_physical as physical_quantity',
                
                // Property Card Information
                'pc.issue_transfer_disposal',
                'pc.received_by_name',
                'rei.amount',
                'pc.article',
                'pc.remarks',
                'pc.condition',
                
                // Location Information
                DB::raw("CASE 
                    WHEN l.building_name IS NOT NULL THEN 
                        CASE 
                            WHEN l.office_name IS NOT NULL 
                            THEN CONCAT(l.building_name, ' - ', l.office_name)
                            ELSE l.building_name
                        END
                    ELSE 'Not Specified' 
                END as location"),
                
                // Additional useful information
                'pc.property_card_id',
                DB::raw("CASE WHEN pc.property_card_id IS NOT NULL THEN 1 ELSE 0 END as has_property_card")
            )
            ->where('rei.item_id', $itemId)
            ->first();
    
        if (!$itemDetails) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Item not found'], 404);
            }
            abort(404, 'Item not found');
        }
    
        // Return JSON for AJAX request or view for regular request
        if (request()->ajax()) {
            return response()->json($itemDetails);
        }
    
        return view('inventory_count_form.item_details', compact('itemDetails'));
    }
    
    public function editItemDetails($inventoryFormId, $itemId)
    {
        // Get the same detailed information as showItemDetails
        $itemDetails = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
            ->leftJoin('property_cards as pc', function($join) use ($inventoryFormId) {
                $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                     ->where('pc.inventory_count_form_id', '=', $inventoryFormId);
            })
            ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
            ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
            ->select(
                // Entity Information
                'e.entity_name',
                
                // Property Information
                'rei.property_no',
                'rei.item_id',
                'rei.description_id', // Add this for updates
                DB::raw("CASE 
                     WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL
                     THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
                    ELSE NULL 
                 END as new_property_no"),
                
                // Description and Details
                'red.description',
                'rei.date_acquired',
                're.par_no',
                'rei.serial_no',
                'red.unit',
                
                // Quantity Information
                'red.quantity as original_quantity',
                'pc.qty_physical as physical_quantity',
                
                // Property Card Information
                'pc.issue_transfer_disposal',
                'pc.received_by_name',
                'rei.amount',
                'pc.article',
                'pc.remarks',
                'pc.condition',
                'pc.locations_id',
                
                // Location Information
                DB::raw("CASE 
                     WHEN l.building_name IS NOT NULL THEN
                         CASE 
                             WHEN l.office_name IS NOT NULL
                             THEN CONCAT(l.building_name, ' - ', l.office_name)
                            ELSE l.building_name
                        END
                    ELSE 'Not Specified'
                 END as location"),
                
                // Additional useful information
                'pc.property_card_id',
                DB::raw("CASE WHEN pc.property_card_id IS NOT NULL THEN 1 ELSE 0 END as has_property_card")
            )
            ->where('rei.item_id', $itemId)
            ->first();
    
        if (!$itemDetails) {
            abort(404, 'Item not found');
        }
    
        // Get all available locations for dropdown
        $locations = DB::table('locations')
            ->select('id', 'building_name', 'office_name')
            ->orderBy('building_name')
            ->orderBy('office_name')
            ->get();
    
        // Format locations for dropdown
        $locationOptions = $locations->map(function($location) {
            return [
                'id' => $location->id,
                'name' => $location->office_name 
                    ? $location->building_name . ' - ' . $location->office_name
                    : $location->building_name
            ];
        });
    
        return view('inventory_count_form.edit_item_details', compact('itemDetails', 'locationOptions', 'inventoryFormId'));
    }
    
    public function updateItemDetails(Request $request, $inventoryFormId, $itemId)
    {
        $request->validate([
            'description' => 'nullable|string|max:500',
            'serial_no' => 'nullable|string|max:100',
            'date_acquired' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'physical_quantity' => 'nullable|integer|min:0',
            'condition' => 'nullable|in:Good,Fair,Poor,New',
            'article' => 'nullable|string|max:200',
            'issue_transfer_disposal' => 'nullable|string|max:200',
            'received_by_name' => 'nullable|string|max:100',
            'locations_id' => 'nullable|exists:locations,id',
            'remarks' => 'nullable|string|max:1000'
        ]);
    
        DB::beginTransaction();
        
        try {
            // First, get the description_id for this item
            $itemData = DB::table('received_equipment_item')
                ->where('item_id', $itemId)
                ->first(['description_id', 'date_acquired', 'amount']);
    
            if (!$itemData) {
                throw new \Exception('Item not found');
            }
    
            // Update received_equipment_item table (only update non-null values)
            $itemUpdateData = [];
            
            if ($request->filled('serial_no')) {
                $itemUpdateData['serial_no'] = $request->serial_no;
            }
            
            if ($request->filled('date_acquired')) {
                $itemUpdateData['date_acquired'] = $request->date_acquired;
            }
            
            if ($request->filled('amount')) {
                $itemUpdateData['amount'] = $request->amount;
            }
    
            if (!empty($itemUpdateData)) {
                $itemUpdateData['updated_at'] = now();
                DB::table('received_equipment_item')
                    ->where('item_id', $itemId)
                    ->update($itemUpdateData);
            }
    
            // Update description in received_equipment_description table
            if ($request->filled('description')) {
                DB::table('received_equipment_description')
                    ->where('description_id', $itemData->description_id)
                    ->update([
                        'description' => $request->description,
                        'updated_at' => now()
                    ]);
            }
    
            // Handle property card - check if it exists
            $propertyCard = DB::table('property_cards')
                ->where('received_equipment_item_id', $itemId)
                ->where('inventory_count_form_id', $inventoryFormId)
                ->first();
    
            // Prepare property card data
            $propertyCardData = [];
            
            if ($request->filled('physical_quantity')) {
                $propertyCardData['qty_physical'] = $request->physical_quantity;
            }
            
            if ($request->filled('condition')) {
                $propertyCardData['condition'] = $request->condition;
            }
            
            if ($request->filled('article')) {
                $propertyCardData['article'] = $request->article;
            }
            
            if ($request->filled('issue_transfer_disposal')) {
                $propertyCardData['issue_transfer_disposal'] = $request->issue_transfer_disposal;
            }
            
            if ($request->has('received_by_name')) { // Use has() instead of filled() to allow empty strings
                $propertyCardData['received_by_name'] = $request->received_by_name;
            }
            
            if ($request->filled('locations_id')) {
                $propertyCardData['locations_id'] = $request->locations_id;
            }
            
            if ($request->has('remarks')) { // Use has() instead of filled() to allow empty strings
                $propertyCardData['remarks'] = $request->remarks;
            }
    
            if ($propertyCard) {
                // Update existing property card (only update fields that were provided)
                if (!empty($propertyCardData)) {
                    $propertyCardData['updated_at'] = now();
                    DB::table('property_cards')
                        ->where('property_card_id', $propertyCard->property_card_id)
                        ->update($propertyCardData);
                }
            } else {
                // Create new property card only if we have some meaningful data
                if (!empty($propertyCardData)) {
                    // Set required fields with defaults if not provided
                    $propertyCardData['received_equipment_item_id'] = $itemId;
                    $propertyCardData['inventory_count_form_id'] = $inventoryFormId;
                    
                    // Set defaults for required fields if not provided
                    if (!isset($propertyCardData['qty_physical'])) {
                        $propertyCardData['qty_physical'] = 0;
                    }
                    if (!isset($propertyCardData['condition'])) {
                        $propertyCardData['condition'] = '';
                    }
                    if (!isset($propertyCardData['remarks'])) {
                        $propertyCardData['remarks'] = '';
                    }
                    if (!isset($propertyCardData['issue_transfer_disposal'])) {
                        $propertyCardData['issue_transfer_disposal'] = '';
                    }
                    if (!isset($propertyCardData['received_by_name'])) {
                        $propertyCardData['received_by_name'] = '';
                    }
                    if (!isset($propertyCardData['article'])) {
                        $propertyCardData['article'] = '';
                    }
                    
                    $propertyCardData['created_at'] = now();
                    $propertyCardData['updated_at'] = now();
                    
                    DB::table('property_cards')->insert($propertyCardData);
                }
            }
    
            DB::commit();
    
            return redirect()
                ->route('inventory-count-form.item-details', [$inventoryFormId, $itemId])
                ->with('success', 'Item details updated successfully!');
    
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the actual error for debugging
            Log::error('Failed to update item details: ' . $e->getMessage(), [
                'inventoryFormId' => $inventoryFormId,
                'itemId' => $itemId,
                'request_data' => $request->all()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update item details: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $groupedPropertyCards = PropertyCard::getGroupedPropertyCards();
        $inventoryForms = InventoryCountForm::all(); // or the relevant query
    
        return view('property_cards.index', compact('groupedPropertyCards', 'inventoryForms'));
    }
    

    /**
     * Show the form for creating a new property card.
     */
    public function create()
    {
        $locations = Location::all();
        $entities = Entity::all();
        $receivedEquipmentItems = ReceivedEquipmentItem::with(['receivedEquipmentDescription.receivedEquipment.entity'])
            ->whereDoesntHave('propertyCard')
            ->get();
        
        return view('property_cards.create', compact('locations', 'entities', 'receivedEquipmentItems'));
    }

    /**
     * Store a newly created property card in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'received_equipment_item_id' => 'required|exists:received_equipment_item,item_id',
            'qty_physical' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'issue_transfer_disposal' => 'nullable|string',
            'received_by_name' => 'nullable|string|max:255',
            'article' => 'required|string|max:255',
            'locations_id' => 'required|exists:locations,id',
            'balance' => 'nullable|numeric|min:0'
        ]);

        PropertyCard::create($request->all());

        return redirect()->route('property_cards.index')
            ->with('success', 'Property card created successfully.');
    }


 
    public function edit($id)
    {
        $property_cards = PropertyCard::findOrFail($id);
        $locations = Location::all();
        $entities = Entity::all();
        $receivedEquipmentItems = ReceivedEquipmentItem::with(['receivedEquipmentDescription.receivedEquipment.entity'])->get();
        
        return view('property_cards.edit', compact('property_cards', 'locations', 'entities', 'receivedEquipmentItems'));
    }

    /**
     * Update the specified property card in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'received_equipment_item_id' => 'required|exists:received_equipment_item,item_id',
            'qty_physical' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'issue_transfer_disposal' => 'nullable|string',
            'received_by_name' => 'nullable|string|max:255',
            'article' => 'required|string|max:255',
            'locations_id' => 'required|exists:locations,id',
            'balance' => 'nullable|numeric|min:0'
        ]);

        $property_cards = PropertyCard::findOrFail($id);
        $property_cards->update($request->all());

        return redirect()->route('property_cards.index')
            ->with('success', 'Property card updated successfully.');
    }

    /**
     * Remove the specified property card from storage.
     */
    public function destroy($id)
    {
        $property_cards = PropertyCard::findOrFail($id);
        $property_cards->delete();

        return redirect()->route('property_cards.index')
            ->with('success', 'Property card deleted successfully.');
    }

    /**
     * Generate PDF for a specific property card group.
     */
    public function generatePDF($descriptionId)
    {
        $propertyCards = PropertyCard::getPropertyCardsByDescriptionId($descriptionId);
        $groupedData = PropertyCard::getGroupedPropertyCards()
            ->where('description_id', $descriptionId)
            ->first();

        if (!$groupedData) {
            return redirect()->route('property_cards.index')
                ->with('error', 'Property cards not found.');
        }

        $pdf = PDF::loadView('property_cards.pdf', compact('propertyCards', 'groupedData'));
        
        return $pdf->download('property_card_' . $descriptionId . '.pdf');
    }

    /**
     * Print view for a specific property card group.
     */
    public function printView($descriptionId)
    {
        $propertyCards = PropertyCard::getPropertyCardsByDescriptionId($descriptionId);
        $groupedData = PropertyCard::getGroupedPropertyCards()
            ->where('description_id', $descriptionId)
            ->first();

        if (!$groupedData) {
            return redirect()->route('property_cards.index')
                ->with('error', 'Property cards not found.');
        }

        return view('property_cards.print', compact('propertyCards', 'groupedData'));
    }

    /**
     * Get property card data via API for specific description_id.
     */
    public function getPropertyCardData($descriptionId)
    {
        $propertyCards = PropertyCard::getPropertyCardsByDescriptionId($descriptionId);
        $groupedData = PropertyCard::getGroupedPropertyCards()
            ->where('description_id', $descriptionId)
            ->first();

        return response()->json([
            'grouped_data' => $groupedData,
            'property_cards' => $propertyCards
        ]);
    }

    /**
     * Get all unique descriptions for dropdown/selection.
     */
    public function getUniqueDescriptions()
    {
        $descriptions = PropertyCard::getUniqueDescriptions();
        return response()->json($descriptions);
    }
}