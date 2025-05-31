<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyCard extends Model
{
    use HasFactory;

    protected $table = 'property_cards';
    protected $primaryKey = 'property_card_id';

    protected $fillable = [
        'inventory_count_form_id',
        'received_equipment_item_id',
        'qty_physical',
        'condition',
        'remarks',
        'issue_transfer_disposal',
        'received_by_name',
        'article',
        'locations_id',
    ];

    protected $casts = [
        'qty_physical' => 'integer',
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function inventoryCountForm()
    {
        return $this->belongsTo(InventoryCountForm::class, 'inventory_count_form_id', 'id');
    }

  public function receivedEquipmentItem()
  {
      return $this->belongsTo(ReceivedEquipmentItem::class, 'received_equipment_item_id', 'item_id');
  }

  public function location()
  {
      return $this->belongsTo(Location::class, 'locations_id', 'id');
  }


  // Accessor for formatted location
  public function getFormattedLocationAttribute()
  {
      if ($this->location) {
          if ($this->location->office_name) {
              return $this->location->building_name . ' - ' . $this->location->office_name;
          }
          return $this->location->building_name;
      }
      return 'Not Specified';
  }

  // Scope for specific inventory form
  public function scopeForInventoryForm($query, $inventoryFormId)
  {
      return $query->where('inventory_count_form_id', $inventoryFormId);
  }

  // Scope for specific condition
  public function scopeByCondition($query, $condition)
  {
      return $query->where('condition', $condition);
  }

    // Accessor for getting equipment description through the relationship chain
    public function getDescriptionAttribute()
    {
        return $this->receivedEquipmentItem?->receivedEquipmentDescription?->description;
    }

    public function getDescriptionIdAttribute()
    {
        return $this->receivedEquipmentItem?->description_id;
    }

    public function getPropertyNumberAttribute()
    {
        return $this->receivedEquipmentItem?->property_no;
    }

    public function getDateAcquiredAttribute()
    {
        return $this->receivedEquipmentItem?->receivedEquipmentDescription?->receivedEquipment?->date_acquired;
    }

    public function getAmountAttribute()
    {
        return $this->receivedEquipmentItem?->receivedEquipmentDescription?->receivedEquipment?->amount;
    }

    public function getEntityNameAttribute()
    {
        return $this->receivedEquipmentItem?->receivedEquipmentDescription?->receivedEquipment?->entity?->entity_name;
    }

    public function getReceiptQuantityAttribute()
    {
        return $this->receivedEquipmentItem?->receivedEquipmentDescription?->quantity;
    }

    // Static method to get grouped property cards by description_id
   public static function getGroupedPropertyCards()
{
    return DB::table('property_cards as pc')
        ->join('received_equipment_item as rei', 'pc.received_equipment_item_id', '=', 'rei.item_id')
        ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
        ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
        ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
        ->join('locations as l', 'pc.locations_id', '=', 'l.id')
        ->select([
            'red.description_id',
            'red.description',
            'e.entity_name',
            're.date_acquired',
            're.amount',
            're.par_no', // ✅ Include par_no from received_equipment
            'red.quantity as receipt_quantity',
            'l.building_name',
            'l.office_name',
            'l.officer_name',
            DB::raw('GROUP_CONCAT(rei.property_no ORDER BY rei.property_no ASC) as property_numbers'),
            DB::raw('MIN(rei.property_no) as first_property_no'),
            DB::raw('MAX(rei.property_no) as last_property_no'),
            DB::raw('SUM(pc.qty_physical) as total_qty_physical'),
            DB::raw('AVG(pc.balance) as avg_balance'),
            'pc.condition',
            'pc.remarks',
            'pc.issue_transfer_disposal',
            'pc.received_by_name',
            'pc.article'
        ])
        ->groupBy([
            'red.description_id',
            'red.description',
            'e.entity_name',
            're.date_acquired',
            're.amount',
            're.par_no', // ✅ Add to groupBy
            'red.quantity',
            'l.building_name',
            'l.office_name',
            'l.officer_name',
            'pc.condition',
            'pc.remarks',
            'pc.issue_transfer_disposal',
            'pc.received_by_name',
            'pc.article'
        ])
        ->get()
        ->map(function ($item) {
    // Extract first and last 4-digit sequence numbers from property numbers
    $firstParts = explode('-', $item->first_property_no);
    $lastParts = explode('-', $item->last_property_no);

    $firstSeq = isset($firstParts[3]) ? $firstParts[3] : '0000';
    $lastSeq = isset($lastParts[3]) ? $lastParts[3] : '0000';

    // Get building initials: first letters of each word
    $words = preg_split('/\s+/', trim($item->building_name));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper($word[0]);
        }
    }

    // Construct PAR code
    $item->par = $initials . '-' . $firstSeq . '-' . $lastSeq;

    return $item;
});

}


    // Static method to get property cards for a specific description_id
    public static function getPropertyCardsByDescriptionId($descriptionId)
    {
        return self::whereHas('receivedEquipmentItem', function ($query) use ($descriptionId) {
            $query->where('description_id', $descriptionId);
        })->with(['receivedEquipmentItem.receivedEquipmentDescription.receivedEquipment.entity', 'location'])->get();
    }

    // Static method to get all unique description_ids with their basic info
    public static function getUniqueDescriptions()
    {
        return DB::table('property_cards as pc')
            ->join('received_equipment_item as rei', 'pc.received_equipment_item_id', '=', 'rei.item_id')
            ->join('received_equipment_description as red', 'rei.description_id', '=', 'red.description_id')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->join('entities as e', 're.entity_id', '=', 'e.entity_id')
            ->select([
                'red.description_id',
                'red.description',
                'e.entity_name',
                DB::raw('COUNT(pc.property_card_id) as total_cards')
            ])
            ->groupBy(['red.description_id', 'red.description', 'e.entity_name'])
            ->get();
    }
}