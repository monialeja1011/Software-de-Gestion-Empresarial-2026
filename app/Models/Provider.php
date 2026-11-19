<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Provider extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'active'
    ];

    protected $casts = ['active' => 'boolean'];

    // Relación: un proveedor tiene muchas compras
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
