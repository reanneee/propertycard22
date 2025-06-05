<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivedEquipmentDescription extends Model
{
    use HasFactory;

    protected $table = 'received_equipment_description';
    protected $primaryKey = 'description_id';

    protected $fillable = [
        'equipment_id',
        'description',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the parent equipment
     */

     
    public function equipment()
    {
        return $this->belongsTo(ReceivedEquipment::class, 'equipment_id', 'equipment_id');
    }

    /**
     * Alternative method name for consistency (keeping both for backward compatibility)
     */
  public function receivedEquipment()
{
    return $this->belongsTo(ReceivedEquipment::class, 'equipment_id', 'equipment_id');
}

    /**
     * Get the items for this description
     */
    public function items()
    {
        return $this->hasMany(ReceivedEquipmentItem::class, 'description_id', 'description_id');
    }

    /**
     * Helper methods
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->count();
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('amount');
    }

    public function getFormattedQuantityAttribute()
    {
        return $this->quantity . ($this->unit ? ' ' . $this->unit : '');
    }
}