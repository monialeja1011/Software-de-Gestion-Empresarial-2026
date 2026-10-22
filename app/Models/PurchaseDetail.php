<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'received_quantity',
        'price',
        'subtotal',
        'iva_percentage',
        'iva_included',
        'base_subtotal',
        'iva_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_percentage' => 'decimal:2',
        'iva_included' => 'boolean',
        'base_subtotal' => 'decimal:2',
        'iva_amount' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function receiptDetails()
    {
        return $this->hasMany(PurchaseReceiptDetail::class);
    }
}