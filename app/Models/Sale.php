<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'client_id',
        'payment_method_id',
        'sale_date',
        'total',
        'active'
    ];

    protected $casts = ['sale_date' => 'date', 'active' => 'boolean'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
