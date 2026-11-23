<?php

namespace App\Notifications;

use App\Models\CompanySetting;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Purchase $purchase,
        public bool $complete
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $purchase = $this->purchase->load([
            'provider',
            'paymentMethod',
            'details.product',
        ]);

        $number = $purchase->order_number ?: 'OC-'.$purchase->id;
        $pdf = Pdf::loadView('purchases.pdf', ['purchase' => $purchase])
            ->setPaper('letter')
            ->output();

        return (new MailMessage)
            ->from(config('mail.from.address'), CompanySetting::current()->trade_name)
            ->subject("Mercancía recibida: {$number}")
            ->view('emails.notification', [
                'title' => $this->complete ? 'Recepción completa' : 'Recepción parcial',
                'greeting' => "Hola, {$notifiable->name}",
                'bodyText' => $this->complete
                    ? 'La orden fue recibida completamente y el inventario quedó actualizado.'
                    : 'Se registró una recepción parcial y el inventario quedó actualizado con las unidades recibidas.',
                'details' => [
                    ['label' => 'Orden de compra', 'value' => $number],
                    ['label' => 'Proveedor', 'value' => $purchase->provider?->name ?? 'No disponible'],
                    ['label' => 'Total de la orden', 'value' => '$'.number_format((float) $purchase->total, 2, ',', '.')],
                    ['label' => 'Estado', 'value' => $this->complete ? 'Recibida' : 'Parcialmente recibida'],
                ],
                'actionUrl' => route('purchases.show', $purchase),
                'actionText' => 'Ver la orden',
            ])
            ->attachData($pdf, "{$number}.pdf", ['mime' => 'application/pdf']);
    }

    public function toArray(object $notifiable): array
    {
        $number = $this->purchase->order_number ?: 'OC-'.$this->purchase->id;

        return [
            'title' => $this->complete ? 'Compra recibida' : 'Recepción parcial',
            'message' => $this->complete
                ? "La orden {$number} fue recibida completa."
                : "Se recibió una parte de la orden {$number}.",
            'url' => route('purchases.show', $this->purchase),
        ];
    }
}
