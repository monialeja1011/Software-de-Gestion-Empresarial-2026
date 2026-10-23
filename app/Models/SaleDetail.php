<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'lot_id',
        'quantity',
        'price',
        'subtotal',
        'iva_percentage',
        'iva_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'lot_id' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_percentage' => 'decimal:2',
        'iva_amount' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}