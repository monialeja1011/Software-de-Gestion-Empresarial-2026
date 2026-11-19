<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $movements = InventoryMovement::with(['product', 'user'])
            ->when($request->filled('product_id'), fn ($query) =>
                $query->where('product_id', $request->integer('product_id'))
            )
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $products = Product::where('active', true)
            ->orderBy('name')
            ->get();

        return view('inventory.kardex', compact('movements', 'products'));
    }

    public function editAdjustment(Product $product): View
    {
        $lastMovements = InventoryMovement::with(['product', 'user'])
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
}
