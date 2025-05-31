<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceivedEquipmentDescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PropertyNumberService;

class ReceivedEquipmentDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
{
    // Use Eloquent with eager loading
    $allDescriptions = ReceivedEquipmentDescription::with(['equipment', 'items'])
        ->orderByDesc('description_id')
        ->get();

    // Group descriptions by PAR number
    $groupedDescriptions = $allDescriptions->groupBy(function ($description) {
        return $description->equipment->par_no ?? 'No PAR';
    });

    // Apply pagination manually since we're grouping
    $currentPage = request()->get('page', 1);
    $perPage = 5; // Number of PAR groups per page
    $pagedGroups = $groupedDescriptions->forPage($currentPage, $perPage);
    
    // Create a custom paginator
    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $pagedGroups,
        $groupedDescriptions->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'pageName' => 'page',
        ]
    );

    // Get all items for fund matching
    $allItemIds = $allDescriptions->flatMap(function ($desc) {
        return $desc->items->pluck('item_id');
    });

    $fundMatches = DB::table('received_equipment_item as rei')
        ->join('funds as f', function($join) {
            $join->on(DB::raw("REPLACE(SUBSTRING(rei.property_no, 6, 5), '-', '')"), '=', DB::raw("SUBSTRING(f.account_code, 4, 4)"));
        })
        ->whereIn('rei.item_id', $allItemIds)
        ->select('rei.item_id', 'rei.property_no', 'f.account_code', 'f.account_title')
        ->get()
        ->keyBy('item_id');

    return view('descriptions.index', compact('pagedGroups', 'fundMatches', 'paginator'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available equipment for the dropdown
        $equipments = DB::table('received_equipment')
            ->select('equipment_id', 'par_no', 'description')
            ->orderBy('par_no')
            ->get();

        return view('descriptions.create', compact('equipments'));
    }

    public function show(string $id)
    {
        //
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