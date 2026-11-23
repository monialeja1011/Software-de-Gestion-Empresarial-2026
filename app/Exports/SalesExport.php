<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Sale::withTrashed()
            ->with(['client', 'paymentMethod', 'user'])
            ->when($this->filters['from'] ?? null, fn ($query, $date) => $query->whereDate('sale_date', '>=', $date))
            ->when($this->filters['to'] ?? null, fn ($query, $date) => $query->whereDate('sale_date', '<=', $date))
            ->latest('id');
    }

    public function headings(): array
    {
        return ['Factura', 'Fecha', 'Cliente', 'Vendedor', 'Medio de pago', 'Subtotal', 'IVA', 'Total', 'Estado'];
    }

    public function map($sale): array
    {
        $cancelled = $sale->status === 'anulada' || ! $sale->active || $sale->trashed();

        return [
            $sale->invoice_number ?? 'Venta #'.$sale->id,
            $sale->sale_date?->format('Y-m-d'),
            $sale->client?->name ?? 'Cliente no disponible',
            $sale->user?->name ?? 'No registrado',
            $sale->paymentMethod?->name ?? '—',
            (float) $sale->subtotal,
            (float) $sale->tax,
            (float) $sale->total,
            $cancelled ? 'Anulada' : ucfirst((string) $sale->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
