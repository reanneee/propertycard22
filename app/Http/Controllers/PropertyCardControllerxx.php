<?php

namespace App\Http\Controllers;

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
    /**
     * Display a listing of grouped property cards.
     */
    public function index()
    {
        $groupedPropertyCards = PropertyCard::getGroupedPropertyCards();
        return view('property_cards.index', compact('groupedPropertyCards'));
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

    /**
     * Display the specified property card group.
     */
    public function show($descriptionId)
    {
        $propertyCards = PropertyCard::getPropertyCardsByDescriptionId($descriptionId);
        
        if ($propertyCards->isEmpty()) {
            return redirect()->route('property_cards.index')
                ->with('error', 'Property cards not found.');
        }

        $groupedData = PropertyCard::getGroupedPropertyCards()
            ->where('description_id', $descriptionId)
            ->first();

        return view('property_cards.show', compact('propertyCards', 'groupedData'));
    }

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