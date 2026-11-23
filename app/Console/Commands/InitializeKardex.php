<?php

namespace App\Console\Commands;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitializeKardex extends Command
{
    protected $signature = 'kardex:inicializar';

    protected $description = 'Registra el stock actual como saldo inicial del kardex';

    public function handle(): int
    {
        $registered = 0;
        $skipped = 0;

        Product::withTrashed()->chunkById(100, function ($products) use (&$registered, &$skipped) {
            foreach ($products as $product) {
                $alreadyHasMovements = InventoryMovement::where('product_id', $product->id)
                    ->exists();

                if ($alreadyHasMovements || (int) $product->stock < 1) {
                    $skipped++;

                    continue;
                }

                DB::transaction(function () use ($product) {
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'entrada',
                        'concept' => 'saldo_inicial',
                        'quantity' => (int) $product->stock,
                        'balance' => (int) $product->stock,
                        'unit_cost' => $product->cost_price,
                        'user_id' => null,
                        'notes' => 'Saldo inicial al implementar el kardex.',
                    ]);
                });

                $registered++;
            }
        });

        $this->info("Saldos iniciales registrados: {$registered}");
        $this->line("Productos omitidos (sin stock o con movimientos previos): {$skipped}");

        return self::SUCCESS;
    }
}
