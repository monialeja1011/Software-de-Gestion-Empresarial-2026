<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private array $filters = [])
    {
    }

    public function query(): Builder
    {
        return Product::query()
            ->with('category')
            ->when($this->filters['buscar'] ?? null, function ($query, $search) {
                $query->where(function ($products) use ($search) {
                    $products->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($categories) => $categories->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($this->filters['categoria_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when(($this->filters['estado'] ?? null) === 'activo', fn ($query) => $query->where('active', true))
            ->when(($this->filters['estado'] ?? null) === 'inactivo', fn ($query) => $query->where('active', false))
            ->orderBy('name');
    }

    public function headings(): array
    {
        return ['Nombre', 'Descripción', 'Precio', 'Stock', 'Categoría'];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->description,
            (float) $product->price,
            (int) $product->stock,
            $product->category?->name,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
