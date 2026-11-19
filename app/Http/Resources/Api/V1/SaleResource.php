<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'factura' => $this->invoice_number,
            'cliente_id' => $this->client_id,
            'usuario_id' => $this->user_id,
            'fecha' => $this->sale_date?->toDateString(),
            'subtotal' => (float) $this->subtotal,
            'iva' => (float) $this->tax,
            'total' => (float) $this->total,
            'estado' => $this->status,
            'detalle' => $this->whenLoaded('details', fn () => $this->details
                ->map(fn ($detail) => [
                    'producto_id' => (int) $detail->product_id,
                    'producto' => $detail->product?->name,
                    'cantidad' => (int) $detail->quantity,
                    'precio' => (float) $detail->price,
                    'subtotal' => (float) $detail->subtotal,
                    'iva_porcentaje' => (float) $detail->iva_percentage,
                    'iva' => (float) $detail->iva_amount,
                ])->values()),
            'pdf' => route('sales.pdf', $this->id),
        ];
    }
}
