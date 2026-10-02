<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'active'
    ];

    // Relación: un proveedor tiene muchas compras
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
