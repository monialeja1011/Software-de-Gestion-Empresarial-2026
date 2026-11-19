<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Client extends Model
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

    // Relación: un cliente tiene muchas ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
