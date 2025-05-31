<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = ['account_title', 'account_code','code'];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function inventoryCountForms()
    {
        return $this->hasMany(InventoryCountForm::class, 'fund_id');
    }
   
    /**
     * Scope for searching by account code
     */
    public function scopeByAccountCode($query, $code)
    {
        return $query->where('account_code', $code);
    }

    /**
     * Scope for searching by code
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Get formatted account display
     */
    public function getAccountDisplayAttribute()
    {
        return $this->account_code . ' - ' . $this->account_title;
    }
}
