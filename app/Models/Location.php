<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable=[
        'id',
        'building_name'
    ];


     public function propertyCards()
    {
        return $this->hasMany(PropertyCard::class, 'locations_id', 'id');
    }
}
