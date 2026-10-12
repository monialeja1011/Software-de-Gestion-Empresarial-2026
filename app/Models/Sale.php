<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'payment_method_id',
        'sale_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
        'active',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_details')
            ->withPivot('quantity', 'price', 'subtotal')
            ->withTimestamps();
    }
}