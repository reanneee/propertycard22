<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ReceivedEquipment extends Model
{
    use HasFactory;

    protected $table = 'received_equipment';
    protected $primaryKey = 'equipment_id';

    protected $fillable = [
        'entity_id',
        'date_acquired',
        'amount',
        'received_by_name',
        'received_by_designation',
        'verified_by_name',
        'verified_by_designation',
        'receipt_date',
        'par_no',
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationship with Entity
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'entity_id');
    }

    // Relationship with ReceivedEquipmentDescription
    public function descriptions()
    {
        return $this->hasMany(ReceivedEquipmentDescription::class, 'equipment_id', 'equipment_id');
    }

    // Keep existing items relationship (direct relationship)
    public function items()
    {
        return $this->hasMany(ReceivedEquipmentItem::class, 'equipment_id', 'equipment_id');
    }

    // Helper method to get all items through descriptions
    public function allItems()
    {
        return $this->hasManyThrough(
            ReceivedEquipmentItem::class,
            ReceivedEquipmentDescription::class,
            'equipment_id', // Foreign key on descriptions table
            'description_id', // Foreign key on items table
            'equipment_id', // Local key on equipment table
            'description_id' // Local key on descriptions table
        );
    }

    // Keep existing boot method for PAR number generation
    public static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            if (empty($equipment->par_no)) {
                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('m');

                $serial = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count() + 1;

                $serialFormatted = str_pad($serial, 4, '0', STR_PAD_LEFT);
                $equipment->par_no = "{$year}-{$month}-{$serialFormatted}";
            }
        });
    }

    // Helper methods
    public function getTotalItemsAttribute()
    {
        return $this->descriptions->sum(function($description) {
            return $description->items->count();
        });
    }

    public function getTotalQuantityAttribute()
    {
        return $this->descriptions->sum('quantity');
    }
}