<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (Auth::check()) {
            $model->created_by = Auth::id();
        }

        if (empty($model->par_no)) {
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');

            $serial = self::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count() + 1;

            $serialFormatted = str_pad($serial, 4, '0', STR_PAD_LEFT);
            $model->par_no = "{$year}-{$month}-{$serialFormatted}";
        }
    });

    static::updating(function ($model) {
        if (Auth::check()) {
            $model->updated_by = Auth::id();
        }
    });
}

    // Relationships
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'entity_id');
    }

    public function descriptions()
    {
        return $this->hasMany(ReceivedEquipmentDescription::class, 'equipment_id', 'equipment_id');
    }

    public function items()
    {
        return $this->hasMany(ReceivedEquipmentItem::class, 'equipment_id', 'equipment_id');
    }

    public function allItems()
    {
        return $this->hasManyThrough(
            ReceivedEquipmentItem::class,
            ReceivedEquipmentDescription::class,
            'equipment_id',
            'description_id',
            'equipment_id',
            'description_id'
        );
    }

    public function getTotalItemsAttribute()
    {
        return $this->descriptions->sum(function ($description) {
            return $description->items->count();
        });
    }

    public function getTotalQuantityAttribute()
    {
        return $this->descriptions->sum('quantity');
    }

     public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
