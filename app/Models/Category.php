<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    // Relación: una categoría tiene muchos productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
