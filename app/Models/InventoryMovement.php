<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'concept',
        'quantity',
        'balance',
        'unit_cost',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo()->constrain([
            Sale::class => fn ($query) => $query->withTrashed(),
        ]);
    }

    public function conceptLabel(): string
    {
        return match ($this->concept) {
            'saldo_inicial' => 'Saldo inicial',
            'venta' => 'Venta',
            'anulacion_venta' => 'Anulación de venta',
            'recepcion_compra' => 'Recepción de compra',
            'ajuste_manual' => 'Ajuste manual',
            default => ucfirst(str_replace('_', ' ', (string) $this->concept)),
        };
    }
}
