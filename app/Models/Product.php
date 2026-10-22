<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $attributes = [
        'markup_percentage' => 20,
        'iva_percentage' => 0,
    ];

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'markup_percentage',
        'iva_percentage',
        'stock',
        'category_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'iva_percentage' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}