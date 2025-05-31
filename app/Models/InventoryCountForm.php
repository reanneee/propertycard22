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

/**
 * Get the fund associated with the inventory count form.
 */
public function fund()
{
    return $this->belongsTo(Fund::class, 'fund_id');
}



/**
 * Get all received equipment items through property cards.
 */
public function receivedEquipmentItems()
{
    return $this->hasManyThrough(
        ReceivedEquipmentItem::class,
        PropertyCard::class,
        'inventory_count_form_id', // Foreign key on property_cards table
        'item_id', // Foreign key on received_equipment_item table
        'id', // Local key on inventory_count_form table
        'received_equipment_item_id' // Local key on property_cards table
    );
}

/**
 * Scope for filtering by entity
 */
public function scopeByEntity($query, $entityId)
{
    return $query->where('entity_id', $entityId);
}

/**
 * Scope for filtering by fund
 */
public function scopeByFund($query, $fundId)
{
    return $query->where('fund_id', $fundId);
}

/**
 * Scope for filtering by date range
 */
public function scopeByDateRange($query, $startDate, $endDate)
{
    return $query->whereBetween('inventory_date', [$startDate, $endDate]);
}

/**
 * Get formatted inventory date
 */
public function getFormattedInventoryDateAttribute()
{
    return $this->inventory_date ? $this->inventory_date->format('M d, Y') : null;
}

/**
 * Check if inventory form has property cards
 */
public function hasPropertyCards()
{
    return $this->propertyCards()->exists();
}

/**
 * Get total items count
 */
public function getTotalItemsCount()
{
    return $this->propertyCards()->count();
}

/**
 * Get total value of all items
 */
public function getTotalValue()
{
    return $this->receivedEquipmentItems()
        ->sum('received_equipment_item.amount');
}

}