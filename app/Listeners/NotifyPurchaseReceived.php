<?php

namespace App\Listeners;

use App\Events\PurchaseReceived;
use App\Models\User;
use App\Notifications\PurchaseReceivedNotification;
use Illuminate\Support\Facades\Notification;

class NotifyPurchaseReceived
{
    public function handle(PurchaseReceived $event): void
    {
        $recipients = User::permission('ver-compras')->get();

        Notification::send(
            $recipients,
            new PurchaseReceivedNotification($event->purchase, $event->complete)
        );
    }
}
