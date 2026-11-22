<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'concept' => ['nullable', 'in:saldo_inicial,venta,anulacion_venta,recepcion_compra,ajuste_manual'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $products = Product::withTrashed()->orderBy('name')->get();
        $selected = ! empty($filters['product_id'])
            ? $products->firstWhere('id', (int) $filters['product_id'])
            : null;

        $movementQuery = InventoryMovement::query()
            ->when($filters['product_id'] ?? null, fn ($query, $id) => $query->where('product_id', $id))
            ->when($filters['concept'] ?? null, fn ($query, $concept) => $query->where('concept', $concept))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));

        $movements = (clone $movementQuery)
            ->with(['product', 'user', 'reference'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $concepts = $this->concepts();
        $totals = null;

        if ($selected) {
            $lastBalance = InventoryMovement::where('product_id', $selected->id)
                ->latest('id')
                ->value('balance');

            $totals = [
                'entradas' => (clone $movementQuery)->where('type', 'entrada')->sum('quantity'),
                'salidas' => (clone $movementQuery)->where('type', 'salida')->sum('quantity'),
                'cuadra' => (int) ($lastBalance ?? 0) === (int) $selected->stock,
            ];
        }

        return view('inventory.kardex', compact(
            'movements',
            'products',
            'concepts',
            'totals'
        ));
    }

    public function editAdjustment(Product $product): View
    {
        $lastMovements = InventoryMovement::with(['product', 'user', 'reference'])
            ->where('product_id', $product->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('inventory.adjust', compact('product', 'lastMovements'));
    }

    public function adjust(
        StoreAdjustmentRequest $request,
        Product $product,
        InventoryService $inventory
    ): RedirectResponse {
        $data = $request->validated();
        $difference = (int) $data['counted_stock'] - (int) $product->stock;

        if ($difference === 0) {
            return back()->withErrors([
                'counted_stock' => 'El nuevo saldo debe ser diferente al stock actual.',
            ]);
        }

        $inventory->record(
            $product,
            $difference > 0 ? 'entrada' : 'salida',
            abs($difference),
            'ajuste_manual',
            null,
            $request->user()->id,
            $product->cost_price !== null ? (float) $product->cost_price : null,
            $data['reason']
        );

        return redirect()->route('inventory.index')
            ->with('success', "Stock de {$product->name} ajustado correctamente.");
    }

    public function pdf(Request $request, Product $product)
    {
        $filters = $request->validate([
            'concept' => ['nullable', 'in:saldo_inicial,venta,anulacion_venta,recepcion_compra,ajuste_manual'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $movements = InventoryMovement::with(['product', 'user', 'reference'])
            ->where('product_id', $product->id)
            ->when($filters['concept'] ?? null, fn ($query, $concept) => $query->where('concept', $concept))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $totals = [
            'entradas' => $movements->where('type', 'entrada')->sum('quantity'),
            'salidas' => $movements->where('type', 'salida')->sum('quantity'),
        ];

        return Pdf::loadView('inventory.pdf', compact('product', 'movements', 'totals', 'filters'))
            ->setPaper('a4', 'landscape')
            ->download('kardex-producto-'.$product->id.'.pdf');
    }

    private function concepts(): array
    {
        return [
            'saldo_inicial' => 'Inventario inicial',
            'venta' => 'Venta',
            'anulacion_venta' => 'Anulación de venta',
            'recepcion_compra' => 'Recepción de compra',
            'ajuste_manual' => 'Ajuste manual',
        ];
    }
}
