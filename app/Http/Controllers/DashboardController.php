<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Entities
        $totalEntities = DB::table('entities')->count();

        // Total Equipment Items (from received_equipment_item table)
        $totalEquipmentItems = DB::table('received_equipment_item')->count();

        // Total Property Cards
        $totalPropertyCards = DB::table('property_cards')->count();

        // Available Items (property cards where received_by_name is blank/null)
        $availableItems = DB::table('property_cards')
            ->where(function($query) {
                $query->whereNull('received_by_name')
                      ->orWhere('received_by_name', '');
            })
            ->count();

        // Active Branches (count distinct branch_id from entities)
        $activeBranches = DB::table('entities')
            ->distinct('branch_id')
            ->count('branch_id');

        // Assigned Items (property cards where received_by_name is not blank)
        $assignedItems = DB::table('property_cards')
            ->where('received_by_name', '!=', '')
            ->whereNotNull('received_by_name')
            ->count();

        // Total Received Equipment
        $totalReceivedEquipment = DB::table('received_equipment')->count();

        // Total Inventory Forms
        $totalInventoryForms = DB::table('inventory_count_form')->count();

        return view('auth.dashboard', compact(
            'totalEntities',
            'totalEquipmentItems', 
            'totalPropertyCards',
            'availableItems',
            'activeBranches',
            'assignedItems',
            'totalReceivedEquipment',
            'totalInventoryForms'
        ));
    }
}
