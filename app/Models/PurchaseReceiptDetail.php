<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceiptDetail extends Model
{
    protected $fillable = [
        'purchase_receipt_id',
        'purchase_detail_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function receipt()
    {
        return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id');
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseDetail::class);
    }
}