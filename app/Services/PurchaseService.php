<?php

namespace App\Services;

use App\Events\PurchaseReceived;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReceipt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    /**
     * Crea una orden con sus detalles. La orden por sí sola no mueve el stock.
     */
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $termDays = (int) ($data['payment_term_days'] ?? 0);

            $purchase = Purchase::create([
                'provider_id' => $data['provider_id'],
                'payment_method_id' => $data['payment_method_id'],
                'purchase_date' => $data['purchase_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'payment_term_days' => $termDays,
                'due_date' => $termDays > 0
                    ? Carbon::parse($data['purchase_date'])
                        ->addDays($termDays)
                        ->toDateString()
                    : $data['purchase_date'],
                'observations' => $data['observations'] ?? null,
                'total' => 0,
                'active' => true,
                'status' => 'enviada',
                'payment_status' => 'pendiente',
            ]);

            $purchase->update([
                'order_number' => 'OC-'.str_pad(
                    (string) $purchase->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $price = round((float) $item['price'], 2);
                $ivaPercentage = (float) $item['iva_percentage'];
                $ivaIncluded = filter_var(
                    $item['iva_included'],
                    FILTER_VALIDATE_BOOLEAN
                );

                if ($ivaIncluded && $ivaPercentage > 0) {
                    $baseUnitPrice = round(
                        $price / (1 + ($ivaPercentage / 100)),
                        2
                    );
                    $ivaUnitAmount = round($price - $baseUnitPrice, 2);
                } else {
                    $baseUnitPrice = $price;
                    $ivaUnitAmount = round(
                        $price * ($ivaPercentage / 100),
                        2
                    );
                }

                $baseSubtotal = round($baseUnitPrice * $quantity, 2);
                $ivaAmount = round($ivaUnitAmount * $quantity, 2);
                $subtotal = round($baseSubtotal + $ivaAmount, 2);

                $purchase->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'received_quantity' => 0,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'iva_percentage' => $ivaPercentage,
                    'iva_included' => $ivaIncluded,
                    'base_subtotal' => $baseSubtotal,
                    'iva_amount' => $ivaAmount,
                ]);

                $total += $subtotal;
            }

            $purchase->update(['total' => round($total, 2)]);

            return $purchase;
        });
    }

    /**
     * Registra una recepción total o parcial y mueve solo las unidades recibidas.
     */
    public function receive(Purchase $purchase, array $data, int $userId): void
    {
        DB::transaction(function () use ($purchase, $data, $userId) {
            $lockedPurchase = Purchase::whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPurchase->status === 'anulada') {
                throw ValidationException::withMessages([
                    'purchase' => 'No se puede recibir una orden anulada.',
                ]);
            }

            $details = $lockedPurchase->details()
                ->with('product')
                ->lockForUpdate()
                ->get();

            $receivedItems = [];

            foreach ($details as $detail) {
                $quantity = (int) ($data['items'][$detail->id]['quantity'] ?? 0);

                if ($quantity === 0) {
                    continue;
                }

                $remaining = $detail->quantity - $detail->received_quantity;

                if ($quantity > $remaining) {
                    throw ValidationException::withMessages([
                        "items.{$detail->id}.quantity" => 'La cantidad recibida supera lo pendiente para '
                            ."{$detail->product?->name}. Pendiente: {$remaining}.",
                    ]);
                }

                $receivedItems[] = [
                    'detail' => $detail,
                    'quantity' => $quantity,
                ];
            }

            if ($receivedItems === []) {
                throw ValidationException::withMessages([
                    'items' => 'Indica una cantidad recibida mayor que cero.',
                ]);
            }

            $receipt = PurchaseReceipt::create([
                'purchase_id' => $lockedPurchase->id,
                'user_id' => $userId,
                'received_at' => now(),
                'observations' => $data['observations'] ?? null,
            ]);

            foreach ($receivedItems as $receivedItem) {
                $detail = $receivedItem['detail'];
                $quantity = $receivedItem['quantity'];

                $receipt->details()->create([
                    'purchase_detail_id' => $detail->id,
                    'quantity' => $quantity,
                ]);

                $detail->increment('received_quantity', $quantity);

                $product = Product::withTrashed()
                    ->whereKey($detail->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'purchase' => 'No se encontró uno de los productos de la orden.',
                    ]);
                }

                $cost = $detail->quantity > 0
                    ? round((float) $detail->base_subtotal / $detail->quantity, 2)
                    : 0;

                $this->inventoryService->record(
                    $product,
                    'entrada',
                    $quantity,
                    'recepcion_compra',
                    $receipt,
                    $userId,
                    $cost,
                    $receipt->observations
                );

                // Actualiza costo y precio público usando el margen del producto
                // y su tarifa de IVA de venta.
                $markup = (float) $product->markup_percentage;
                $salePriceBeforeIva = round(
                    $cost * (1 + ($markup / 100)),
                    2
                );

                $saleIvaPercentage = (float) $product->iva_percentage;
                $publicPrice = round(
                    $salePriceBeforeIva * (1 + ($saleIvaPercentage / 100)),
                    2
                );

                $product->update([
                    'cost_price' => $cost,
                    'price' => $publicPrice,
                ]);
            }

            $allReceived = $lockedPurchase->details()
                ->whereColumn('received_quantity', '<', 'quantity')
                ->doesntExist();

            $lockedPurchase->update([
                'status' => $allReceived ? 'recibida' : 'parcialmente_recibida',
                'received_at' => $allReceived ? now() : null,
            ]);

            PurchaseReceived::dispatch($lockedPurchase, $allReceived);
        });
    }

    /**
     * Anula una orden solo cuando aún no se ha recibido mercancía.
     */
    public function cancel(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $lockedPurchase = Purchase::whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPurchase->status === 'anulada') {
                throw ValidationException::withMessages([
                    'purchase' => 'La orden ya estaba anulada.',
                ]);
            }

            if ($lockedPurchase->details()
                ->where('received_quantity', '>', 0)
                ->exists()) {
                throw ValidationException::withMessages([
                    'purchase' => 'No se puede anular una orden que ya tiene productos recibidos.',
                ]);
            }

            $lockedPurchase->update([
                'active' => false,
                'status' => 'anulada',
            ]);
        });
    }
}
