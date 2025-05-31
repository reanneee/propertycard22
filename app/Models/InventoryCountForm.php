<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryCountForm extends Model
{
    use HasFactory;

    protected $table = 'inventory_count_form';
    protected $primaryKey = 'id';

    protected $fillable = [
       'title',
        'fund_id',
        'entity_id',
        'inventory_date',
        'prepared_by_name',
        'prepared_by_position',
        'reviewed_by_name',
        'reviewed_by_position',
    ];
    protected $casts = [
        'inventory_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with entity
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'entity_id');
    }

    // Relationship with property cards (inventory items)
    public function propertyCards()
    {
        return $this->hasMany(PropertyCard::class, 'inventory_count_form_id');
    }

    public function inventoryCountForm()
{
    return $this->hasOne(InventoryCountForm::class, 'entity_id', 'entity_id');
}

// Entity.php Model - add this method  
public function inventoryCountForms()
{
    return $this->hasMany(InventoryCountForm::class, 'entity_id', 'entity_id');
}

public function fund()
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    
    /**
     * Scope to filter by entity
     */
    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * Scope to filter by fund
     */
    public function scopeByFund($query, $fundId)
    {
        return $query->where('fund_id', $fundId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('inventory_date', [$startDate, $endDate]);
    }



public function receivedEquipmentItem()
{
    return $this->belongsTo(ReceivedEquipmentItem::class, 'received_equipment_item_id', 'item_id');
}


}