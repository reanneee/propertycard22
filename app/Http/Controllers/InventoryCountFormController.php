<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Http\Request;
use App\Models\InventoryCountForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReceivedEquipmentDescription;
use Barryvdh\DomPDF\Facade\Pdf;
class InventoryCountFormController extends Controller
{
    public function index(Request $request)
    {
        // Build the query with relationships
        $query = InventoryCountForm::with(['entity', 'fund'])
            ->withCount(['propertyCards as items_count']);
    
        // Apply search filters
        if ($request->filled('search_title')) {
            $query->where('title', 'like', '%' . $request->search_title . '%');
        }
    
        if ($request->filled('filter_entity')) {
            $query->where('entity_id', $request->filter_entity);
        }
    
        if ($request->filled('filter_fund')) {
            $query->where('fund_id', $request->filter_fund);
        }
    
        // Apply date filters
        if ($request->filled('filter_date')) {
            switch ($request->filter_date) {
                case 'today':
                    $query->whereDate('inventory_date', today());
                    break;
                case 'week':
                    $query->whereBetween('inventory_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('inventory_date', now()->month)
                          ->whereYear('inventory_date', now()->year);
                    break;
                case 'year':
                    $query->whereYear('inventory_date', now()->year);
                    break;
            }
        }
    
        // Order by latest first
        $query->orderBy('created_at', 'desc');
    
        // Paginate results
        $inventoryForms = $query->paginate(15);
    
        // Get filter data
        $entities = DB::table('entities')->orderBy('entity_name')->get();
        $funds = DB::table('funds')->orderBy('account_code')->get();
    
        // Calculate statistics
        $totalForms = InventoryCountForm::count();
        $totalItems = DB::table('property_cards')
            ->join('inventory_count_form', function($join) {
                // Fixed: Use the correct column name from schema
                $join->on('property_cards.inventory_count_form_id', '=', 'inventory_count_form.id');
            })
            ->count();
        
        $thisMonth = InventoryCountForm::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Calculate total value - FIXED: Use 'amount' instead of 'unit_value'
        $totalValue = DB::table('property_cards')
            ->join('received_equipment_item', 'property_cards.received_equipment_item_id', '=', 'received_equipment_item.item_id')
            ->sum(DB::raw('property_cards.qty_physical * received_equipment_item.amount'));
    
        return view('inventory_count_form.index', compact(
            'inventoryForms',
            'entities', 
            'funds',
            'totalForms',
            'totalItems',
            'thisMonth',
            'totalValue'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */public function createInventory(Request $request)
{
    // Debug: Log the incoming request data
    Log::info('Received request data:', [
        'selected_items' => $request->selected_items,
        'quantities' => $request->quantities,
        'entity_id' => $request->entity_id, // Add this
        'par_no' => $request->par_no,      // Add this
        'all_input' => $request->all()
    ]);
    
    $request->validate([
        'selected_items' => 'required|array',
        'selected_items.*' => 'exists:received_equipment_description,description_id',
        'quantities' => 'required|array',
        'quantities.*' => 'integer|min:1',
        'entity_id' => 'required|exists:entities,entity_id', // Add validation
        'par_no' => 'required|string'  // Add validation
    ]);
    
    $selectedDescriptionIds = $request->selected_items;
    $quantities = $request->quantities;
    $entityId = $request->entity_id;  // Capture entity_id
    $parNo = $request->par_no;        // Capture par_no
    
    // Debug: Log the processed data
    Log::info('Processed data:', [
        'selectedDescriptionIds' => $selectedDescriptionIds,
        'quantities' => $quantities,
        'entity_id' => $entityId,
        'par_no' => $parNo
    ]);
    
    // Get selected descriptions with their items and equipment details
    $descriptions = DB::table('received_equipment_description as red')
        ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
        ->whereIn('red.description_id', $selectedDescriptionIds)
        ->select('red.*', 're.par_no', 're.amount')
        ->get();
    
    // Get items for selected descriptions with additional details
    $items = DB::table('received_equipment_item')
        ->whereIn('description_id', $selectedDescriptionIds)
        ->get()
        ->groupBy('description_id');
    
    // Attach items to descriptions and prepare inventory rows
    foreach ($descriptions as $description) {
        $allItems = $items->get($description->description_id, collect());
        $inventoryQuantity = $quantities[$description->description_id] ?? $description->quantity;
        
        // Take only the number of items specified in inventory_quantity
        $description->items = $allItems->take($inventoryQuantity);
        $description->inventory_quantity = $inventoryQuantity;
        $description->total_available = $allItems->count(); // Total available items
        
        // Calculate unit value if not set (total amount divided by total quantity)
        if ($description->amount && $description->quantity > 0) {
            $description->unit_value = $description->amount / $description->quantity;
        } else {
            $description->unit_value = 0;
        }
        
        // Debug: Log each description's quantity assignment
        Log::info("Description {$description->description_id}: total_available={$description->total_available}, inventory_quantity={$description->inventory_quantity}, actual_rows={$description->items->count()}, unit_value={$description->unit_value}");
    }
    
    // Get fund matches for article/item classification
    $fundMatches = DB::table('received_equipment_item as rei')
        ->join('funds as f', function($join) {
            // Extract MM-DD from property_no (positions 5-9) and compare with 4th-7th digits of account_code
            $join->on(DB::raw("REPLACE(SUBSTRING(rei.property_no, 6, 5), '-', '')"), '=', DB::raw("SUBSTRING(f.account_code, 4, 4)"));
        })
        ->select('rei.item_id', 'rei.property_no', 'f.account_code', 'f.account_title', 'f.id as fund_id')
        ->get()
        ->keyBy('item_id');
    
    // Get existing linked equipment items with constructed new property numbers
    $linkedItems = DB::table('linked_equipment_items')
        ->select(
            'original_property_no', 
            'reference_mmdd', 
            'new_property_no', 
            'location', 
            'id',
            'created_at',
            // Construct the full new property number: YEAR-reference_mmdd-new_property_no-location
            DB::raw("CONCAT(YEAR(created_at), '-', reference_mmdd, '-', new_property_no, '-', location) as full_new_property_no")
        )
        ->get()
        ->keyBy('original_property_no');
    
    // Get equipment items for location information
    $equipmentItems = DB::table('equipment_items')
        ->select('property_no', 'location_id', 'status')
        ->get();
    
    // Store data in session for the GET create method
    session([
        'processedDescriptions' => $descriptions,
        'fundMatches' => $fundMatches,
        'quantities' => $quantities,
        'equipmentItems' => $equipmentItems,
        'linkedItems' => $linkedItems,
        'selected_entity_id' => $entityId,  // Store entity_id in session
        'par_no' => $parNo                  // Store par_no in session
    ]);
    
    // Redirect to the GET create route
    return redirect()->route('inventory.create');
}

public function create()
{
    // Check if we have processed descriptions from the previous step
    $processedDescriptions = session('processedDescriptions');
    $fundMatches = session('fundMatches', collect());
    $equipmentItems = session('equipmentItems', collect());
    $linkedItems = session('linkedItems', collect());
    $quantities = session('quantities', []);
    $selectedEntityId = session('selected_entity_id'); // Get entity_id from session
    $parNo = session('par_no');                        // Get par_no from session
    
    // If no processed descriptions in session, redirect back to descriptions index
    if (!$processedDescriptions || $processedDescriptions->isEmpty()) {
        return redirect()->route('descriptions.index')
            ->with('error', 'No equipment selected for inventory. Please select equipment first.');
    }
    $funds = Fund::orderBy('account_code')->get();
    // Get all locations for dropdown
    $locations = DB::table('locations')
        ->select('id', 'building_name', 'office_name', 'officer_name')
        ->orderBy('building_name')
        ->orderBy('office_name')
        ->get();
    
    // Get entities for dropdown
    $entities = DB::table('entities')
        ->select('entity_id', 'entity_name')
        ->orderBy('entity_name')
        ->get();
    
    // Get the selected entity details for display
    $selectedEntity = null;
    if ($selectedEntityId) {
        $selectedEntity = DB::table('entities')
            ->where('entity_id', $selectedEntityId)
            ->first();
    }
    
    return view('inventory_count_form.create', compact(
        'processedDescriptions', 
        'fundMatches', 
        'quantities', 
        'equipmentItems', 
        'locations', 
        'entities',
        'linkedItems',
        'selectedEntityId',    // Pass selected entity ID
        'selectedEntity',      // Pass selected entity details
        'parNo',
        'funds',                // Pass PAR number
    ));
}
    // Add this method to save/update linked equipment items via AJAX

/**
 * Store a newly created resource in storage.
 */

 public function store(Request $request)
 {
     try {
         // Start database transaction
         DB::beginTransaction();
         
         // Validate the request - Add title and fund_id validation
         $request->validate([
             'title' => 'required|string|max:255',
             'fund_id' => 'required|exists:funds,id', // Assuming your funds table has 'id' as primary key
             'entity_id' => 'required|exists:entities,entity_id',
             'inventory_date' => 'required|date',
             'inventory_items' => 'required|array|min:1',
             'inventory_items.*.article_item' => 'required|string',
             'inventory_items.*.description' => 'required|string',
             'inventory_items.*.old_property_no' => 'required|string',
             'inventory_items.*.new_property_no' => 'nullable|string',
             'inventory_items.*.unit' => 'required|string',
             'inventory_items.*.unit_value' => 'required|numeric|min:0',
             'inventory_items.*.qty_card' => 'required|integer|min:0',
             'inventory_items.*.qty_physical' => 'required|integer|min:0',
             'inventory_items.*.location' => 'required|string',
             'inventory_items.*.location_id' => 'required|integer|exists:locations,id',
             'inventory_items.*.condition' => 'required|string',
             'inventory_items.*.remarks' => 'nullable|string',
             'prepared_by_name' => 'nullable|string|max:255',
             'prepared_by_position' => 'nullable|string|max:255',
             'reviewed_by_name' => 'nullable|string|max:255',
             'reviewed_by_position' => 'nullable|string|max:255',
         ]);
 
         // Create the main inventory count form record with title and fund_id
         $inventoryCountForm = InventoryCountForm::create([
             'title' => $request->title,
             'fund_id' => $request->fund_id,
             'entity_id' => $request->entity_id,
             'inventory_date' => $request->inventory_date,
             'prepared_by_name' => $request->prepared_by_name,
             'prepared_by_position' => $request->prepared_by_position,
             'reviewed_by_name' => $request->reviewed_by_name,
             'reviewed_by_position' => $request->reviewed_by_position,
         ]);
 
         // Store each inventory item in property_cards table
         foreach ($request->inventory_items as $index => $item) {
             // First, get the received_equipment_item_id based on old_property_no
             $receivedEquipmentItem = DB::table('received_equipment_item')
                 ->where('property_no', $item['old_property_no'])
                 ->first();
 
             if ($receivedEquipmentItem) {
                 // Use the location_id directly from the form
                 $locationId = (int) $item['location_id'];
                 
                 // Verify the location exists (optional extra check)
                 $locationExists = DB::table('locations')->where('id', $locationId)->exists();
                 
                 if (!$locationExists) {
                     Log::warning('Location ID does not exist, using default', [
                         'submitted_location_id' => $locationId,
                         'item_index' => $index,
                         'old_property_no' => $item['old_property_no']
                     ]);
                     $locationId = 1; // Default location
                 }
 
                 // Log for debugging
                 Log::info('Using location ID for property card', [
                     'item_index' => $index,
                     'old_property_no' => $item['old_property_no'],
                     'location_id' => $locationId,
                     'location_string' => $item['location']
                 ]);
 
                 // Insert into property_cards table with inventory_count_form_id
                 DB::table('property_cards')->insert([
                     'inventory_count_form_id' => $inventoryCountForm->id, // Add this line
                     'received_equipment_item_id' => $receivedEquipmentItem->item_id,
                     'qty_physical' => $item['qty_physical'],
                     'condition' => $item['condition'],
                     'remarks' => $item['remarks'] ?? '',
                     'issue_transfer_disposal' => '', // Default value, adjust as needed
                     'received_by_name' => '',
                     'article' => $item['article_item'],
                     'locations_id' => $locationId, // Use the location_id directly
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]);
 
                 Log::info('Property card inserted successfully', [
                     'item_index' => $index,
                     'old_property_no' => $item['old_property_no'],
                     'final_location_id' => $locationId,
                     'received_equipment_item_id' => $receivedEquipmentItem->item_id,
                     'inventory_count_form_id' => $inventoryCountForm->id // Add this to logging
                 ]);
 
             } else {
                 // Log if received_equipment_item not found
                 Log::warning('Received equipment item not found', [
                     'property_no' => $item['old_property_no'],
                     'item_index' => $index
                 ]);
             }
         }
 
         // Log the successful creation with new fields
         Log::info('Inventory Count Form created successfully', [
             'form_id' => $inventoryCountForm->id,
             'title' => $request->title,
             'fund_id' => $request->fund_id,
             'entity_id' => $request->entity_id,
             'total_items' => count($request->inventory_items),
             'inventory_date' => $request->inventory_date
         ]);
 
         // Commit the transaction
         DB::commit();
 
         // Clear the session data
         session()->forget(['processedDescriptions', 'fundMatches', 'quantities', 'equipmentItems', 'linkedItems']);
 
         // Redirect with success message including title
         return redirect()->route('inventory.index')
             ->with('success', 'Inventory Count Form "' . $request->title . '" created successfully! Total items: ' . count($request->inventory_items));
 
     } catch (\Illuminate\Validation\ValidationException $e) {
         DB::rollBack();
         Log::error('Validation failed for inventory count form', [
             'errors' => $e->errors(),
             'request_data' => $request->all()
         ]);
         
         return redirect()->back()
             ->withErrors($e->errors())
             ->withInput();
 
     } catch (\Exception $e) {
         DB::rollBack();
         Log::error('Error creating inventory count form: ' . $e->getMessage(), [
             'trace' => $e->getTraceAsString(),
             'request_data' => $request->all()
         ]);
 
         return redirect()->back()
             ->with('error', 'An error occurred while saving the inventory count form. Please try again.')
             ->withInput();
     }
 }

 public function show($id)
 {
     // Get the inventory count form details using Eloquent relationships
     $inventoryForm = InventoryCountForm::with([
         'entity.branch',
         'entity.fundCluster', 
         'fund'
     ])->find($id);
 
     if (!$inventoryForm) {
         abort(404, 'Inventory form not found');
     }
 
     // Get detailed inventory items with property card information and PAR details
     $inventoryItems = DB::table('received_equipment_item as rei')
         ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
         ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
         ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
         ->leftJoin('property_cards as pc', function($join) use ($id) {
             $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                  ->where('pc.inventory_count_form_id', '=', $id);
         })
         ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
         ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
         ->select(
             // Basic item information
             'rei.item_id',
             'e.entity_name',
             'red.description as article_description',
             'pc.article',
             'rei.property_no as old_property_no',
             DB::raw("CASE 
                 WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
                 THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
                 ELSE NULL 
             END as new_property_no"),
             'red.unit',
             'rei.amount as unit_value',
             'rei.date_acquired',
             'rei.serial_no',
             
             // PAR information
             're.par_no',
             
             // Quantity information
             'red.quantity as original_quantity',
             'pc.qty_physical as quantity_per_property_card',
             'pc.qty_physical as quantity_per_physical_count',
             
             // Property card specific information
             'pc.condition',
             'pc.remarks',
             'pc.issue_transfer_disposal',
             'pc.received_by_name',
             
             // Location information
             DB::raw("CASE 
                 WHEN l.building_name IS NOT NULL THEN 
                     CASE 
                         WHEN l.office_name IS NOT NULL 
                         THEN CONCAT(l.building_name, ' - ', l.office_name)
                         ELSE l.building_name
                     END
                 ELSE 'Not Specified' 
             END as location_whereabouts"),
             
             // Property card existence flag - Modified to use received_by_name
             'pc.property_card_id',
             DB::raw("CASE 
                 WHEN pc.received_by_name IS NOT NULL AND pc.received_by_name != '' 
                 THEN 1 
                 ELSE 0 
             END as has_property_card")
         )
         ->where('e.entity_id', $inventoryForm->entity_id)
         ->orderBy('red.description')
         ->orderBy('rei.property_no')
         ->get();
 
     // Group items by description for better organization
     $groupedItems = $inventoryItems->groupBy('article_description');
 
     // Statistics - Updated to use the new logic
     $totalItems = $inventoryItems->count();
     $itemsWithPropertyCards = $inventoryItems->where('has_property_card', 1)->count();
     $itemsWithoutPropertyCards = $totalItems - $itemsWithPropertyCards;
     
     // Calculate total value
     $totalValue = $inventoryItems->sum('unit_value');
 
     return view('inventory_count_form.show', compact(
         'inventoryForm', 
         'inventoryItems', 
         'groupedItems',
         'totalItems',
         'itemsWithPropertyCards', 
         'itemsWithoutPropertyCards',
         'totalValue'
     ));
 }
public function saveLinkedEquipmentItem(Request $request)
{
    try {
        $request->validate([
            'original_property_no' => 'required|string',
            'reference_mmdd' => 'required|string',
            'new_property_no' => 'required|string',
            'location' => 'required|string',
        ]);

        // Check if record exists
        $existingRecord = DB::table('linked_equipment_items')
            ->where('original_property_no', $request->original_property_no)
            ->first();

        if ($existingRecord) {
            // Update existing record
            DB::table('linked_equipment_items')
                ->where('id', $existingRecord->id)
                ->update([
                    'reference_mmdd' => $request->reference_mmdd,
                    'new_property_no' => $request->new_property_no,
                    'location' => $request->location,
                    'year' => date('Y'), // Add current year
                    'updated_at' => now(),
                ]);
        } else {
            // For new records, we need to get the fund_id
            // This requires linking to the funds table based on some criteria
            // You may need to adjust this based on your business logic
            $fundId = 1; // Default fund_id - adjust as needed
            
            // Create new record
            DB::table('linked_equipment_items')->insert([
                'fund_id' => $fundId,
                'original_property_no' => $request->original_property_no,
                'reference_mmdd' => $request->reference_mmdd,
                'new_property_no' => $request->new_property_no,
                'year' => date('Y'),
                'location' => $request->location,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Error saving linked equipment item: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}


   
// public function showItemDetails($inventoryFormId, $itemId)
// {
//     // Get detailed information for a specific item
//     $itemDetails = DB::table('received_equipment_item as rei')
//         ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
//         ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
//         ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
//         ->leftJoin('property_cards as pc', function($join) use ($inventoryFormId) {
//             $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
//                  ->where('pc.inventory_count_form_id', '=', $inventoryFormId);
//         })
//         ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
//         ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
//         ->select(
//             // Entity Information
//             'e.entity_name',
            
//             // Property Information
//             'rei.property_no',
//             DB::raw("CASE 
//                 WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
//                 THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
//                 ELSE NULL 
//             END as new_property_no"),
            
//             // Description and Details
//             'red.description',
//             'rei.date_acquired',
//             're.par_no',
//             'rei.serial_no',
//             'red.unit',
            
//             // Quantity Information
//             'red.quantity as original_quantity',
//             'pc.qty_physical as physical_quantity',
            
//             // Property Card Information
//             'pc.issue_transfer_disposal',
//             'pc.received_by_name',
//             'rei.amount',
//             'pc.article',
//             'pc.remarks',
//             'pc.condition',
            
//             // Location Information
//             DB::raw("CASE 
//                 WHEN l.building_name IS NOT NULL THEN 
//                     CASE 
//                         WHEN l.office_name IS NOT NULL 
//                         THEN CONCAT(l.building_name, ' - ', l.office_name)
//                         ELSE l.building_name
//                     END
//                 ELSE 'Not Specified' 
//             END as location"),
            
//             // Additional useful information
//             'pc.property_card_id',
//             DB::raw("CASE WHEN pc.property_card_id IS NOT NULL THEN 1 ELSE 0 END as has_property_card")
//         )
//         ->where('rei.item_id', $itemId)
//         ->first();

//     if (!$itemDetails) {
//         if (request()->ajax()) {
//             return response()->json(['error' => 'Item not found'], 404);
//         }
//         abort(404, 'Item not found');
//     }

//     // Return JSON for AJAX request or view for regular request
//     if (request()->ajax()) {
//         return response()->json($itemDetails);
//     }

//     return view('inventory_count_form.item_details', compact('itemDetails'));
// }


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

    // Process remarks and condition for PDF
    $combinedRemarks = $this->combineRemarksAndCondition($itemDetails->remarks, $itemDetails->condition);
    $itemDetails->combined_remarks = $combinedRemarks;

    // Return JSON for AJAX request or view for regular request     
    if (request()->ajax()) {         
        return response()->json($itemDetails);     
    }      

    return view('inventory_count_form.item_details', compact('itemDetails')); 
}

/**
 * Generate PDF for property card
 */
public function generatePDF($inventoryFormId, $itemId)
{
    // Get the same item details
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
            'e.entity_name',                          
            'rei.property_no',             
            DB::raw("CASE                  
                WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL                  
                THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)                 
                ELSE NULL              
            END as new_property_no"),                          
            'red.description',             
            'rei.date_acquired',             
            're.par_no',             
            'rei.serial_no',             
            'red.unit',                          
            'red.quantity as original_quantity',             
            'pc.qty_physical as physical_quantity',                          
            'pc.issue_transfer_disposal',             
            'pc.received_by_name as received_by_name',             
            'rei.amount',             
            'pc.article',             
            'pc.remarks',             
            'pc.condition',                          
            DB::raw("CASE                  
                WHEN l.building_name IS NOT NULL THEN                      
                    CASE                          
                        WHEN l.office_name IS NOT NULL                          
                        THEN CONCAT(l.building_name, ' - ', l.office_name)                         
                        ELSE l.building_name                     
                    END                 
                ELSE 'Not Specified'              
            END as location"),                          
            'pc.property_card_id',             
            DB::raw("CASE WHEN pc.property_card_id IS NOT NULL THEN 1 ELSE 0 END as has_property_card")         
        )         
        ->where('rei.item_id', $itemId)         
        ->first();

    if (!$itemDetails) {
        abort(404, 'Item not found');
    }

    // Combine remarks and condition
    $itemDetails->combined_remarks = $this->combineRemarksAndCondition($itemDetails->remarks, $itemDetails->condition);

    // Generate PDF
    $pdf = PDF::loadView('inventory_count_form.property_card_pdf', compact('itemDetails'));
    
    // Set paper size and orientation
    $pdf->setPaper('A4', 'portrait');
    
    // Generate filename
    $filename = 'Property_Card_' . ($itemDetails->property_no ?? 'Unknown') . '_' . date('Y-m-d') . '.pdf';
    
    return $pdf->download($filename);
}

/**
 * Helper method to combine remarks and condition
 */
private function combineRemarksAndCondition($remarks, $condition)
{
    $combinedParts = [];
    
    // Add remarks if exists
    if (!empty($remarks) && trim($remarks) !== '') {
        $combinedParts[] = trim($remarks);
    }
    
    // Add condition if exists and not "Good" (assuming Good is default)
    if (!empty($condition) && trim($condition) !== '' && strtolower($condition) !== 'good') {
        $combinedParts[] = 'Condition: ' . trim($condition);
    }
    
    // Join with line break or separator
    return implode(' | ', $combinedParts);
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


    public function generateReport($id)
    {
        // Alternative method for generating a more detailed report
        $inventoryForm = DB::table('inventory_count_form as icf')
            ->join('entities as e', 'icf.entity_id', '=', 'e.entity_id')
            ->join('branches as b', 'e.branch_id', '=', 'b.branch_id')
            ->join('fund_clusters as fc', 'e.fund_cluster_id', '=', 'fc.id')
            ->join('funds as f', 'icf.fund_id', '=', 'f.id')
            ->select(
                'icf.*',
                'e.entity_name',
                'b.branch_name',
                'fc.name as fund_cluster_name',
                'f.account_code as fund_account_code'
            )
            ->where('icf.id', $id)
            ->first();

        // Get comprehensive inventory data with totals
        $inventoryData = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
            ->leftJoin('property_cards as pc', 'rei.item_id', '=', 'pc.received_equipment_item_id')
            ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
            ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
            ->select(
                'red.description as article_description',
                'pc.article',
                'rei.property_no as old_property_no',
                DB::raw("CASE 
                    WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
                    THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
                    ELSE 'Not Assigned' 
                END as new_property_no"),
                'red.unit',
                'rei.amount as unit_value',
                'pc.qty_physical as quantity_per_property_card',
                'pc.qty_physical as quantity_per_physical_count',
                DB::raw("CASE 
                    WHEN l.building_name IS NOT NULL THEN 
                        CASE 
                            WHEN l.office_name IS NOT NULL 
                            THEN CONCAT(l.building_name, ' - ', l.office_name)
                            ELSE l.building_name
                        END
                    ELSE 'Not Specified' 
                END as location_whereabouts"),
                'pc.condition',
                'pc.remarks',
                'rei.serial_no',
                'rei.date_acquired',
                DB::raw('(rei.amount * COALESCE(pc.qty_physical, 1)) as total_value')
            )
            ->where('e.entity_id', $inventoryForm->entity_id)
            ->orderBy('red.description')
            ->orderBy('rei.property_no')
            ->get();

        // Calculate summary statistics
        $summary = [
            'total_items' => $inventoryData->count(),
            'total_value' => $inventoryData->sum('total_value'),
            'items_with_new_property_no' => $inventoryData->where('new_property_no', '!=', 'Not Assigned')->count(),
            'items_without_new_property_no' => $inventoryData->where('new_property_no', '=', 'Not Assigned')->count(),
            'condition_summary' => $inventoryData->groupBy('condition')->map->count(),
            'location_summary' => $inventoryData->groupBy('location_whereabouts')->map->count()
        ];

        return view('inventory.report', compact('inventoryForm', 'inventoryData', 'summary'));
    }
 public function print($id)
{
    try {
        // Get the inventory count form details
        $inventoryForm = DB::table('inventory_count_form as icf')
            ->join('entities as e', 'icf.entity_id', '=', 'e.entity_id')
            ->join('branches as b', 'e.branch_id', '=', 'b.branch_id')
            ->join('fund_clusters as fc', 'e.fund_cluster_id', '=', 'fc.id')
            ->join('funds as f', 'icf.fund_id', '=', 'f.id')
            ->select(
                'icf.*',
                'e.entity_name',
                'b.branch_name',
                'fc.name as fund_cluster_name',
                'f.account_code as fund_account_code'
            )
            ->where('icf.id', $id)
            ->first();

        if (!$inventoryForm) {
            abort(404, 'Inventory form not found');
        }

        $inventoryItems = DB::table('received_equipment_item as rei')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
            ->leftJoin('property_cards as pc', function($join) use ($id) {
                $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                     ->where('pc.inventory_count_form_id', '=', $id);
            })
            ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
            ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
            ->select(
                'rei.item_id',
                'red.description as article_description',
                'pc.article',
                'rei.property_no as old_property_no',
                'red.unit',
                'rei.amount as unit_value',
                'rei.serial_no',
                'rei.date_acquired',
                're.par_no',
                
                DB::raw("CASE 
                    WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
                    THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
                    ELSE 'Not Assigned' 
                END as new_property_no"),
                
                // Quantity Information
                'red.quantity as original_quantity',
                'pc.qty_physical as quantity_per_property_card',
                'pc.qty_physical as quantity_per_physical_count',
                
                // LOCATION - Enhanced handling
                'l.id as location_id',
                'l.building_name',
                'l.office_name',
                'l.officer_name',
                DB::raw("CASE 
                    WHEN l.building_name IS NOT NULL THEN 
                        CASE 
                            WHEN l.office_name IS NOT NULL AND l.office_name != '' 
                            THEN CONCAT(l.building_name, ' - ', l.office_name)
                            ELSE l.building_name
                        END
                    ELSE 'Location Not Specified' 
                END as location_whereabouts"),
                
                // CONDITION - Fixed with backticks to escape reserved keyword
                DB::raw("CASE 
                    WHEN pc.condition IS NOT NULL AND pc.condition != '' 
                    THEN pc.condition 
                    ELSE 'Not Specified' 
                END as `condition`"),
                
                // REMARKS - Ensure it's properly selected
                DB::raw("CASE 
                    WHEN pc.remarks IS NOT NULL AND pc.remarks != '' 
                    THEN pc.remarks 
                    ELSE '' 
                END as remarks"),
                
                // Additional Property Card Information
                'pc.issue_transfer_disposal',
                'pc.received_by_name',
                'pc.property_card_id',
                
                // Calculated fields
                DB::raw('(rei.amount * COALESCE(pc.qty_physical, 1)) as total_value'),
                DB::raw("CASE WHEN pc.property_card_id IS NOT NULL THEN 1 ELSE 0 END as has_property_card")
            )
            ->where('e.entity_id', $inventoryForm->entity_id)
            ->orderBy('red.description')
            ->orderBy('rei.property_no')
            ->get();

        // Group items by description for better organization (optional)
        $groupedItems = $inventoryItems->groupBy('article_description');

        // Calculate summary statistics including condition breakdown
        $totalItems = $inventoryItems->count();
        $totalValue = $inventoryItems->sum('total_value');
        $itemsWithPropertyCards = $inventoryItems->where('has_property_card', 1)->count();
        
        // Condition statistics - access using array notation due to reserved keyword
        $conditionSummary = $inventoryItems->groupBy(function($item) {
            return $item->condition;
        })->map(function($items) {
            return $items->count();
        })->toArray();
        
        // Location statistics
        $locationSummary = $inventoryItems->groupBy('location_whereabouts')->map(function($items) {
            return $items->count();
        })->toArray();

        // Log for debugging (optional - remove in production)
        Log::info('Print view data prepared', [
            'inventory_form_id' => $id,
            'total_items' => $totalItems,
            'items_with_location' => $inventoryItems->where('location_whereabouts', '!=', 'Location Not Specified')->count(),
            'items_with_condition' => $inventoryItems->filter(function($item) {
                return $item->condition != 'Not Specified';
            })->count(),
            'items_with_remarks' => $inventoryItems->where('remarks', '!=', '')->count(),
        ]);

        // Return the print view with all necessary data
        return view('inventory_count_form.print', compact(
            'inventoryForm', 
            'inventoryItems', 
            'groupedItems',
            'totalItems',
            'totalValue',
            'itemsWithPropertyCards',
            'conditionSummary',
            'locationSummary'
        ));
        
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error generating print view', [
            'inventory_form_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Handle error - redirect back with error message
        return redirect()->back()->with('error', 'Unable to generate print view: ' . $e->getMessage());
    }
}
   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    
}