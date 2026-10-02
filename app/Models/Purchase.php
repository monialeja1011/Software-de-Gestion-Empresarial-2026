<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'provider_id',
        'purchase_date',
        'total',
        'active'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}

