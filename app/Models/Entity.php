<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $primaryKey = 'entity_id';

    protected $fillable = [
        'entity_name',
        'branch_id',
        'fund_cluster_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function fundCluster()
    {
        return $this->belongsTo(FundCluster::class, 'fund_cluster_id', 'id');
    }

    // Updated relationship name to match your existing model
    public function receivedEquipments()
    {
        return $this->hasMany(ReceivedEquipment::class, 'entity_id', 'entity_id');
    }

    // Keep existing relationships
    public function inventoryCounts()
    {
        return $this->hasMany(InventoryCountForm::class, 'entity_id');
    }

    public function propertyCards()
    {
        return $this->hasMany(PropertyCard::class, 'entity_id');
    }

    // Add helper methods for easier access to related data
    public function getTotalEquipmentRecordsAttribute()
    {
        return $this->receivedEquipments->count();
    }

    public function getTotalAmountAttribute()
    {
        return $this->receivedEquipments->sum('amount');
    }

    public function getTotalItemsAttribute()
    {
        return $this->receivedEquipments->sum(function($equipment) {
            return $equipment->descriptions->sum(function($description) {
                return $description->items->count();
            });
        });
    }
}