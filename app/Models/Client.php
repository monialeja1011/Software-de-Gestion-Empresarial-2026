<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Client extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'document_number',
        'email',
        'phone',
        'address',
        'active',
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

    /** Cédula con puntos de miles (1.088.234.567), o null si aún no se registró. */
    public function formattedDocument(): ?string
    {
        return $this->document_number
            ? preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $this->document_number)
            : null;
    }
}
