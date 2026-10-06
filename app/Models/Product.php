<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'active'
    ];

    protected $casts = ['active' => 'boolean', 'price' => 'decimal:2'];

    // Relación: un producto pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
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
