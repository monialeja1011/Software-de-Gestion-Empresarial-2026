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
            // Si un producto viene en varias filas, suma sus cantidades.
            $quantities = [];

            foreach ($data['items'] as $item) {
                $productId = (int) $item['product_id'];
                $quantities[$productId] = ($quantities[$productId] ?? 0)
                    + (int) $item['quantity'];
            }

            $items = [];
            $subtotal = 0;

            foreach ($quantities as $productId => $quantity) {
                // Bloquea el producto mientras se procesa la venta.
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

                $unitPrice = (float) $product->price;
                $lineSubtotal = round($unitPrice * $quantity, 2);
                $subtotal = round($subtotal + $lineSubtotal, 2);

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $tax = round($subtotal * 0.19, 2);
            $total = round($subtotal + $tax, 2);

            // Se crea primero sin número. Luego se usa el ID para evitar
            // repetir números de factura si dos ventas ocurren a la vez.
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
                'invoice_number' => 'FV-'.str_pad(
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
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return $sale;
        });
    }
}
