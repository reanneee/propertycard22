<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Branch;
use App\Models\FundCluster;

class EntityController extends Controller
{
    public function index()
    {
        $entities = Entity::with([
            'branch', 
            'fundCluster',
            'receivedEquipments.descriptions.items'  // Changed from 'receivedEquipment' to 'receivedEquipments'
        ])->get();
    
        return view('entities.index', compact('entities'));
    }
    
    public function show($id)
    {
        $entity = Entity::with([
            'branch',
            'fundCluster',
            'receivedEquipments' => function($query) {  // Changed from 'receivedEquipment' to 'receivedEquipments'
                $query->with(['descriptions.items']);
            }
        ])->findOrFail($id);
        
        return view('entities.show', compact('entity'));
    }
    
    public function create()
    {
        $branches = Branch::all();
        $fundClusters = FundCluster::all();
    
        return view('entities.create', [
            'branches' => $branches,
            'fundClusters' => $fundClusters
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,branch_id',
            'fund_cluster_id' => 'required|exists:fund_clusters,id',
        ]);
    
        $entity = Entity::create($validated);
    
        return redirect()->route('received_equipment.create_with_entity', ['entityId' => $entity->entity_id])
        ->with('success', 'Entity created successfully. Now add received equipment.');
    }
    
    public function edit($id)
    {
        $entity = Entity::findOrFail($id);
        $branches = Branch::all();
        $fundClusters = FundCluster::all();
    
        return view('entities.edit', compact('entity', 'branches', 'fundClusters'));
    }
    
    public function update(Request $request, $id)
    {
        $entity = Entity::findOrFail($id);
        $entity->update($request->all());
        return redirect()->route('entities.index')->with('success', 'Entity updated successfully.');
    }
    
    public function destroy($id)
    {
        $entity = Entity::findOrFail($id);
        $entity->delete();
        return redirect()->route('entities.index')->with('success', 'Entity deleted successfully.');
    }
}