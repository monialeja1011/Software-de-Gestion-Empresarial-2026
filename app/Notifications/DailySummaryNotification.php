<?php

namespace App\Notifications;

use App\Models\CompanySetting;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $summary)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $summary = $this->summary;
        $money = fn ($amount) => '$'.number_format((float) $amount, 2, ',', '.');
        $details = [
            ['label' => 'Ventas registradas', 'value' => (string) $summary['sales_count']],
            ['label' => 'Total vendido', 'value' => $money($summary['sales_total'])],
            ['label' => 'Compras registradas', 'value' => $money($summary['purchases_total'])],
            ['label' => 'Órdenes pendientes de recibir', 'value' => (string) $summary['pending_purchases']],
        ];

        if ($summary['low_stock'] !== []) {
            $details[] = [
                'label' => 'Productos con stock bajo',
                'value' => implode(', ', $summary['low_stock']),
            ];
        }

        return (new MailMessage)
            ->from(config('mail.from.address'), CompanySetting::current()->trade_name)
            ->subject("Resumen del día {$summary['date']}")
            ->view('emails.notification', [
                'title' => 'Resumen del día',
                'greeting' => "Hola, {$notifiable->name}",
                'bodyText' => "Este es el resumen de la operación del {$summary['date']}.",
                'details' => $details,
                'actionUrl' => route('dashboard'),
                'actionText' => 'Abrir el dashboard',
            ]);
    }
}
