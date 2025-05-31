<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Fund;
use App\Models\PropertyCard;
use App\Models\InventoryCountForm;
use App\Models\Location;
use App\Models\ReceivedEquipmentDescription;
use App\Models\ReceivedEquipmentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyCardController extends Controller
{
    public function index()
    {
        // Get all inventory forms with related data and vacant equipment statistics
        $inventoryForms = DB::table('inventory_count_form as icf')
            ->leftJoin('entities as e', 'icf.entity_id', '=', 'e.entity_id')
            ->leftJoin('branches as b', 'e.branch_id', '=', 'b.branch_id') // Adjust column name as needed
            ->leftJoin('fund_clusters as fc', 'e.fund_cluster_id', '=', 'fc.id')
            ->leftJoin('funds as f', 'icf.fund_id', '=', 'f.id')
            ->select('icf.*', 'e.entity_name', 'b.branch_name', 'fc.name as fund_cluster_name', 'f.account_title')
            ->addSelect([
                // Subquery to count total items per form
                'total_items' => DB::table('received_equipment_item as rei')
                    ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
                    ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
                    ->whereColumn('re.entity_id', 'icf.entity_id')
                    ->selectRaw('COUNT(rei.item_id)'),
                    
                // Subquery to count items with property cards (received_by_name not null)
                'items_with_cards' => DB::table('received_equipment_item as rei')
                    ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
                    ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
                    ->leftJoin('property_cards as pc', function($join) {
                        $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                             ->whereColumn('pc.inventory_count_form_id', 'icf.id');
                    })
                    ->whereColumn('re.entity_id', 'icf.entity_id')
                    ->whereNotNull('pc.received_by_name')
                    ->where('pc.received_by_name', '!=', '')
                    ->selectRaw('COUNT(rei.item_id)'),
                    
                // Subquery to count vacant equipment
                'vacant_equipment_count' => DB::table('received_equipment_item as rei')
                    ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
                    ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
                    ->leftJoin('property_cards as pc', function($join) {
                        $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                             ->whereColumn('pc.inventory_count_form_id', 'icf.id');
                    })
                    ->whereColumn('re.entity_id', 'icf.entity_id')
                    ->where(function($query) {
                        $query->whereNull('pc.received_equipment_item_id')
                              ->orWhereNull('pc.received_by_name')
                              ->orWhere('pc.received_by_name', '=', '');
                    })
                    ->selectRaw('COUNT(rei.item_id)'),
                    
                // Subquery to calculate vacant equipment value
                'vacant_equipment_value' => DB::table('received_equipment_item as rei')
                    ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
                    ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
                    ->leftJoin('property_cards as pc', function($join) {
                        $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
                             ->whereColumn('pc.inventory_count_form_id', 'icf.id');
                    })
                    ->whereColumn('re.entity_id', 'icf.entity_id')
                    ->where(function($query) {
                        $query->whereNull('pc.received_equipment_item_id')
                              ->orWhereNull('pc.received_by_name')
                              ->orWhere('pc.received_by_name', '=', '');
                    })
                    ->selectRaw('COALESCE(SUM(rei.amount), 0)')
            ])
            ->orderBy('icf.created_at', 'desc')
            ->get();
    
        // Calculate summary statistics and add calculated status
        $totalForms = $inventoryForms->count();
        $completedForms = 0;
        $totalVacantEquipment = $inventoryForms->sum('vacant_equipment_count');
        $totalVacantValue = $inventoryForms->sum('vacant_equipment_value');
    
        // Calculate completion percentages and determine status
        $inventoryForms->each(function($form) use (&$completedForms) {
            $form->completion_percentage = $form->total_items > 0 
                ? round(($form->items_with_cards / $form->total_items) * 100, 2)
                : 0;
                
            // Calculate status based on completion and form data
            if ($form->completion_percentage == 100 && 
                !empty($form->prepared_by_name) && 
                !empty($form->reviewed_by_name)) {
                $form->status = 'completed';
                $completedForms++;
            } elseif ($form->completion_percentage > 0 || 
                      !empty($form->prepared_by_name) || 
                      !empty($form->reviewed_by_name)) {
                $form->status = 'pending';
            } else {
                $form->status = 'draft';
            }
        });
    
        // Get entities for filter dropdown
        $entities = DB::table('entities')->select('entity_id', 'entity_name')->get();
    
        return view('property_cards.index', compact(
            'inventoryForms', 
            'totalForms', 
            'completedForms', 
            'totalVacantEquipment', 
            'totalVacantValue',
            'entities'
        ));
    }
    
    private function getPropertyCardStats()
    {
        $total = DB::table('property_cards')->count();
        
        $incomplete = DB::table('property_cards')
            ->where(function($q) {
                $q->whereNull('received_by_name')
                  ->orWhere('received_by_name', '=', '');
            })
            ->count();

        $complete = $total - $incomplete;

        return [
            'total' => $total,
            'complete' => $complete,
            'incomplete' => $incomplete,
            'completion_rate' => $total > 0 ? round(($complete / $total) * 100, 1) : 0
        ];
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

    public function show($id)
    {
        // Get inventory form details
        $inventoryForm = DB::table('inventory_count_form as icf')
            ->join('entities as e', 'icf.entity_id', '=', 'e.entity_id')
            ->leftJoin('funds as f', 'icf.fund_id', '=', 'f.id')
            ->select(
                'icf.*',
                'e.entity_name',
                'f.account_title',
                'f.account_code'
            )
            ->where('icf.id', $id)
            ->first();
    
        if (!$inventoryForm) {
            abort(404, 'Inventory form not found');
        }
    
        // Get all items for this inventory form
        $items = DB::table('property_cards as pc')
            ->join('received_equipment_item as rei', 'pc.received_equipment_item_id', '=', 'rei.item_id')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
            ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
            ->select(
                'pc.property_card_id',
                'rei.item_id',
                'rei.property_no',
                DB::raw("CASE 
                    WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
                    THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
                    ELSE NULL 
                END as new_property_no"),
                'red.description',
                'pc.qty_physical',
                'pc.condition',
                'pc.article',
                'rei.amount',
                DB::raw("CASE 
                    WHEN l.building_name IS NOT NULL THEN 
                        CASE 
                            WHEN l.office_name IS NOT NULL 
                            THEN CONCAT(l.building_name, ' - ', l.office_name)
                            ELSE l.building_name
                        END
                    ELSE 'Not Specified' 
                END as location"),
                'pc.remarks',
                'rei.date_acquired'
            )
            ->where('pc.inventory_count_form_id', $id)
            ->orderBy('red.description')
            ->paginate(20);
    
        return view('inventory_count_form.show', compact('inventoryForm', 'items'));
    }


    /**
     * Display the specified property card group.
     */
    // public function show($id)
    // {
    //     // Get the inventory count form details using Eloquent relationships
    //     $inventoryForm = InventoryCountForm::with([
    //         'entity.branch',
    //         'entity.fundCluster', 
    //         'fund'
    //     ])->find($id);
    
    //     if (!$inventoryForm) {
    //         abort(404, 'Inventory form not found');
    //     }
    
    //     // Get detailed inventory items with property card information and PAR details
    //     $inventoryItems = DB::table('received_equipment_item as rei')
    //         ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
    //         ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
    //         ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
    //         ->leftJoin('property_cards as pc', function($join) use ($id) {
    //             $join->on('rei.item_id', '=', 'pc.received_equipment_item_id')
    //                  ->where('pc.inventory_count_form_id', '=', $id);
    //         })
    //         ->leftJoin('locations as l', 'pc.locations_id', '=', 'l.id')
    //         ->leftJoin('linked_equipment_items as lei', 'rei.property_no', '=', 'lei.original_property_no')
    //         ->select(
    //             // Basic item information
    //             'rei.item_id',
    //             'e.entity_name',
    //             'red.description as article_description',
    //             'pc.article',
    //             'rei.property_no as old_property_no',
    //             DB::raw("CASE 
    //                 WHEN lei.year IS NOT NULL AND lei.reference_mmdd IS NOT NULL AND lei.new_property_no IS NOT NULL AND lei.location IS NOT NULL 
    //                 THEN CONCAT(lei.year, '-', lei.reference_mmdd, '-', lei.new_property_no, '-', lei.location)
    //                 ELSE NULL 
    //             END as new_property_no"),
    //             'red.unit',
    //             'rei.amount as unit_value',
    //             'rei.date_acquired',
    //             'rei.serial_no',
                
    //             // PAR information
    //             're.par_no',
                
    //             // Quantity information
    //             'red.quantity as original_quantity',
    //             'pc.qty_physical as quantity_per_property_card',
    //             'pc.qty_physical as quantity_per_physical_count',
                
    //             // Property card specific information
    //             'pc.condition',
    //             'pc.remarks',
    //             'pc.issue_transfer_disposal',
    //             'pc.received_by_name',
                
    //             // Location information
    //             DB::raw("CASE 
    //                 WHEN l.building_name IS NOT NULL THEN 
    //                     CASE 
    //                         WHEN l.office_name IS NOT NULL 
    //                         THEN CONCAT(l.building_name, ' - ', l.office_name)
    //                         ELSE l.building_name
    //                     END
    //                 ELSE 'Not Specified' 
    //             END as location_whereabouts"),
                
    //             // Property card existence flag
    //             'pc.property_card_id',
    //             DB::raw("CASE WHEN pc.property_card_id IS NOT NULL THEN 1 ELSE 0 END as has_property_card")
    //         )
    //         ->where('e.entity_id', $inventoryForm->entity_id)
    //         ->orderBy('red.description')
    //         ->orderBy('rei.property_no')
    //         ->get();
    
    //     // Group items by description for better organization
    //     $groupedItems = $inventoryItems->groupBy('article_description');
    
    //     // Statistics
    //     $totalItems = $inventoryItems->count();
    //     $itemsWithPropertyCards = $inventoryItems->where('has_property_card', 1)->count();
    //     $itemsWithoutPropertyCards = $totalItems - $itemsWithPropertyCards;
    
    //     return view('inventory_count_form.show', compact(
    //         'inventoryForm', 
    //         'inventoryItems', 
    //         'groupedItems',
    //         'totalItems',
    //         'itemsWithPropertyCards', 
    //         'itemsWithoutPropertyCards'
    //     ));
    // }
    

    /**
     * Show the form for editing the specified property card.
     */
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