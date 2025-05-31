<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivedEquipmentItem extends Model
{
    use HasFactory;

    protected $table = 'received_equipment_item';
    protected $primaryKey = 'item_id';
    
    protected $fillable = [
        'description_id',
        'serial_no',
        'property_no',
        'date_acquired',
        'amount'
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'amount' => 'decimal:2',
    ];

    // /**
    //  * Get the description this item belongs to
    //  */
    // public function description()
    // {
    //     return $this->belongsTo(ReceivedEquipmentDescription::class, 'description_id', 'description_id');
    // }

    /**
     * Get the equipment through description (indirect relationship)
     */
    public function equipment()
    {
        return $this->hasOneThrough(
            ReceivedEquipment::class,
            ReceivedEquipmentDescription::class,
            'description_id', // Foreign key on descriptions table
            'equipment_id', // Foreign key on equipment table
            'description_id', // Local key on items table
            'equipment_id' // Local key on descriptions table
        );
    }

    /**
     * Get the entity through equipment and description
     */
    public function entity()
    {
        return $this->hasOneThrough(
            Entity::class,
            ReceivedEquipment::class,
            'equipment_id', // Foreign key on equipment table
            'entity_id', // Foreign key on entity table
            'equipment_id', // Local key (through description->equipment)
            'entity_id' // Local key on equipment table
        )->through('description.equipment');
    }

    /**
     * Keep existing property card relationship
     */
    public function propertyCard()
    {
        return $this->hasOne(PropertyCard::class, 'received_equipment_item_id', 'item_id');
    }

    /**
     * Helper methods
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function getFormattedDateAcquiredAttribute()
    {
        return $this->date_acquired ? $this->date_acquired->format('M d, Y') : 'N/A';
    }

    public function hasSerialNumber()
    {
        return !empty($this->serial_no);
    }

    public function receivedEquipmentDescription()
{
    return $this->belongsTo(ReceivedEquipmentDescription::class, 'description_id', 'description_id');
}

}