<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyCardApiController extends Controller
{
    /**
     * Get description data for AJAX call in create form
     */
    public function getDescriptionData($description_id)
    {
        try {
            $data = DB::table('received_equipment_description as red')
                ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
                ->join('received_equipment_item as rei', 'red.description_id', '=', 'rei.description_id')
                ->select(
                    're.date_acquired',
                    're.amount',
                    'red.quantity as receipt_quantity',
                    'red.description',
                    DB::raw('GROUP_CONCAT(rei.property_no ORDER BY rei.property_no SEPARATOR ", ") as property_numbers'),
                    DB::raw('MIN(rei.item_id) as received_equipment_item_id')
                )
                ->where('red.description_id', $description_id)
                ->groupBy('re.date_acquired', 're.amount', 'red.quantity', 'red.description')
                ->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for this description'
                ]);
            }

            // Check if there's existing property card data for this description
            $existingData = PropertyCard::getExistingDataForDescription($description_id);

            return response()->json([
                'success' => true,
                'date_acquired' => $data->date_acquired,
                'amount' => $data->amount,
                'property_numbers' => $data->property_numbers,
                'received_equipment_item_id' => $data->received_equipment_item_id,
                'existing_data' => $existingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading description data: ' . $e->getMessage()
            ]);
        }
    }
}