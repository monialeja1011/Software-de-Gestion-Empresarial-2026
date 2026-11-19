<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function record(
        Product $product,
        string $type,
        int $quantity,
        string $concept,
        ?Model $reference = null,
        ?int $userId = null,
        ?float $unitCost = null,
        ?string $notes = null
    ): InventoryMovement {
        if (! in_array($type, ['entrada', 'salida'], true) || $quantity < 1) {
            throw ValidationException::withMessages([
                'inventory' => 'El tipo y la cantidad del movimiento no son válidos.',
            ]);
        }

        return DB::transaction(function () use (
            $product,
            $type,
            $quantity,
            $concept,
            $reference,
            $userId,
            $unitCost,
            $notes
        ) {
            $lockedProduct = Product::withTrashed()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $currentStock = (int) $lockedProduct->stock;

            if ($type === 'salida' && $currentStock < $quantity) {
                throw ValidationException::withMessages([
                    'inventory' => "Stock insuficiente para «{$lockedProduct->name}».",
                ]);
            }

            $newBalance = $type === 'entrada'
                ? $currentStock + $quantity
                : $currentStock - $quantity;

            $lockedProduct->update(['stock' => $newBalance]);

            $movement = new InventoryMovement([
                'type' => $type,
                'concept' => $concept,
                'quantity' => $quantity,
                'balance' => $newBalance,
                'unit_cost' => $unitCost,
                'user_id' => $userId,
                'notes' => $notes,
            ]);

            $movement->product()->associate($lockedProduct);

            if ($reference) {
                $movement->reference()->associate($reference);
            }

            $movement->save();

            return $movement;
        });
    }
}
