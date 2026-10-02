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

    // Relación: un producto pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
