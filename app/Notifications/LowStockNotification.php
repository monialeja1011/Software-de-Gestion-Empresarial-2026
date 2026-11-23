<?php

namespace App\Notifications;

use App\Models\CompanySetting;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->product;

        return (new MailMessage)
            ->from(config('mail.from.address'), CompanySetting::current()->trade_name)
            ->subject("Stock bajo: {$product->name}")
            ->view('emails.notification', [
                'title' => 'Alerta de stock bajo',
                'greeting' => "Hola, {$notifiable->name}",
                'bodyText' => 'Una venta dejó este producto en el nivel mínimo configurado.',
                'details' => [
                    ['label' => 'Producto', 'value' => $product->name],
                    ['label' => 'Existencias actuales', 'value' => (string) $product->stock],
                    ['label' => 'Nivel mínimo', 'value' => (string) config('erp.low_stock_threshold', 5)],
                ],
                'actionUrl' => route('purchases.create'),
                'actionText' => 'Crear orden de compra',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Stock bajo',
            'message' => "{$this->product->name} quedó con {$this->product->stock} unidades.",
            'url' => route('inventory.index', ['product_id' => $this->product->id]),
        ];
    }
}
