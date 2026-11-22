<?php

namespace App\Listeners;

use App\Events\SaleRegistered;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

class CheckLowStock
{
    public function handle(SaleRegistered $event): void
    {
        $threshold = (int) config('erp.low_stock_threshold', 5);
        $event->sale->loadMissing('details.product');

        foreach ($event->sale->details as $detail) {
            $product = $detail->product?->fresh();

            if (! $product) {
                continue;
            }

            $previousStock = (int) $product->stock + (int) $detail->quantity;

            if ($previousStock > $threshold && (int) $product->stock <= $threshold) {
                $recipients = User::permission('crear-compras')->get();

                Notification::send($recipients, new LowStockNotification($product));
            }
        }
    }
}
