<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Purchase extends Model
{
    use LogsActivity;
    protected $fillable = [
        'provider_id',
        'payment_method_id',
        'purchase_date',
        'total',
        'active',
        'order_number',
        'status',
        'expected_delivery_date',
        'payment_term_days',
        'due_date',
        'received_at',
        'observations',
        'payment_status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
        'due_date' => 'date',
        'received_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function receipts()
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
