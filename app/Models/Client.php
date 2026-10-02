<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'active'
    ];

    // Relación: un cliente tiene muchas ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}

