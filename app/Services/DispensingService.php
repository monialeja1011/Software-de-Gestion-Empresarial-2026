<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispensingService
{
    /**
     * Descuenta unidades de los lotes vigentes en orden FEFO
     * y actualiza el stock total del producto.
     *
     * Devuelve cuánto se tomó de cada lote.
     */
    public function dispense(Product $product, int $quantity): array
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad debe ser mayor que cero.',
            ]);
        }

        return DB::transaction(function () use ($product, $quantity) {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lots = Lot::query()
                ->where('product_id', $lockedProduct->id)
                ->whereDate('expiration_date', '>=', today())
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $available = (int) $lots->sum('quantity');

            if ($available < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stock vigente insuficiente. Disponible: {$available}.",
                ]);
            }

            if ((int) $lockedProduct->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'El stock total del producto es insuficiente.',
                ]);
            }

            $remaining = $quantity;
            $dispensed = [];

            foreach ($lots as $lot) {
                if ($remaining === 0) {
                    break;
                }

                $taken = min((int) $lot->quantity, $remaining);

                $lot->quantity -= $taken;
                $lot->save();

                $dispensed[] = [
                    'lot_id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'quantity' => $taken,
                ];

                $remaining -= $taken;
            }

            $lockedProduct->stock -= $quantity;
            $lockedProduct->save();

            return $dispensed;
        }, 3);
    }
}
