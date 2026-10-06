<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'provider_id',
        'payment_method_id',
        'purchase_date',
        'total',
        'active'
    ];

    protected $casts = ['purchase_date' => 'date', 'active' => 'boolean'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}

