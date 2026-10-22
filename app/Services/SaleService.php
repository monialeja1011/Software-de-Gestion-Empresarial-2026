<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function register(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            // Si un producto aparece en varias filas, se consolidan las cantidades.
            $quantities = [];

            foreach ($data['items'] as $item) {
                $productId = (int) $item['product_id'];
                $quantities[$productId] = ($quantities[$productId] ?? 0)
                    + (int) $item['quantity'];
            }

            $items = [];
            $subtotal = 0;
            $tax = 0;
            $total = 0;

            foreach ($quantities as $productId => $quantity) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($productId);

                if (! $product->active) {
                    throw ValidationException::withMessages([
                        'items' => "El producto «{$product->name}» está inactivo.",
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para «{$product->name}». "
                            ."Disponible: {$product->stock}; solicitado: {$quantity}.",
                    ]);
                }

                // El precio público del producto ya incluye el IVA.
                $unitPrice = (float) $product->price;
                $ivaPercentage = (float) $product->iva_percentage;
                $lineTotal = round($unitPrice * $quantity, 2);

                if ($ivaPercentage > 0) {
                    $lineSubtotal = round(
                        $lineTotal / (1 + ($ivaPercentage / 100)),
                        2
                    );
                    $ivaAmount = round($lineTotal - $lineSubtotal, 2);
                } else {
                    $lineSubtotal = $lineTotal;
                    $ivaAmount = 0;
                }

                $subtotal = round($subtotal + $lineSubtotal, 2);
                $tax = round($tax + $ivaAmount, 2);
                $total = round($total + $lineTotal, 2);

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                    'iva_percentage' => $ivaPercentage,
                    'iva_amount' => $ivaAmount,
                ];
            }

            $sale = Sale::create([
                'invoice_number' => null,
                'client_id' => $data['client_id'],
                'user_id' => $userId,
                'payment_method_id' => $data['payment_method_id'],
                'sale_date' => $data['sale_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pagada',
                'notes' => $data['notes'] ?? null,
                'active' => true,
            ]);

            $sale->update([
                'invoice_number' => 'CV-'.str_pad(
                    (string) $sale->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            foreach ($items as $item) {
                $sale->details()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'iva_percentage' => $item['iva_percentage'],
                    'iva_amount' => $item['iva_amount'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return $sale;
        });
    }
}
