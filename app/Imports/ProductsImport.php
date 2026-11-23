<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    private int $created = 0;

    private int $updated = 0;

    private array $failures = [];

    public function __construct(
        private int $userId,
        private InventoryService $inventory
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            if ($row->filter(fn ($value) => trim((string) $value) !== '')->isEmpty()) {
                continue;
            }

            $line = $index + 2;
            $data = [
                'name' => trim((string) ($row['nombre'] ?? '')),
                'description' => trim((string) ($row['descripcion'] ?? '')),
                'price' => $row['precio'] ?? null,
                'stock' => $row['stock'] ?? null,
                'category' => trim((string) ($row['categoria'] ?? '')),
            ];

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:150'],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
                'category' => ['required', 'string', 'max:100'],
            ], [
                'required' => 'El campo :attribute es obligatorio.',
                'string' => 'El campo :attribute debe ser texto.',
                'max.string' => 'El campo :attribute supera el máximo permitido.',
                'numeric' => 'El precio debe ser numérico.',
                'integer' => 'El stock debe ser un número entero.',
                'min.numeric' => 'El precio no puede ser negativo.',
                'min.integer' => 'El stock no puede ser negativo.',
            ], [
                'name' => 'nombre',
                'description' => 'descripción',
                'price' => 'precio',
                'stock' => 'stock',
                'category' => 'categoría',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $column => $messages) {
                    $this->failures[] = [
                        'fila' => $line,
                        'columna' => $column,
                        'errores' => $messages,
                    ];
                }

                continue;
            }

            $category = Category::query()
                ->where('active', true)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['category'])])
                ->first();

            if (! $category) {
                $this->failures[] = [
                    'fila' => $line,
                    'columna' => 'category',
                    'errores' => ['La categoría no existe o está inactiva.'],
                ];

                continue;
            }

            DB::transaction(function () use ($data, $category) {
                $product = Product::query()
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])
                    ->lockForUpdate()
                    ->first();

                if ($product) {
                    $product->update([
                        'price' => $data['price'],
                        'description' => $data['description'] !== '' ? $data['description'] : null,
                        'category_id' => $category->id,
                    ]);

                    $this->updated++;

                    return;
                }

                $initialStock = (int) $data['stock'];
                $product = Product::create([
                    'name' => $data['name'],
                    'description' => $data['description'] !== '' ? $data['description'] : null,
                    'price' => $data['price'],
                    'stock' => 0,
                    'category_id' => $category->id,
                    'active' => true,
                ]);

                if ($initialStock > 0) {
                    $this->inventory->record(
                        $product,
                        'entrada',
                        $initialStock,
                        'saldo_inicial',
                        null,
                        $this->userId,
                        $product->cost_price !== null ? (float) $product->cost_price : null,
                        'Stock inicial al importar el producto.'
                    );
                }

                $this->created++;
            });
        }
    }

    public function createdCount(): int
    {
        return $this->created;
    }

    public function updatedCount(): int
    {
        return $this->updated;
    }

    public function failures(): array
    {
        return $this->failures;
    }
}
