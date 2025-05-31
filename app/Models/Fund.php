<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = ['account_title', 'account_code'];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function inventoryCountForms()
    {
        return $this->hasMany(InventoryCountForm::class, 'fund_id');
    }
}
