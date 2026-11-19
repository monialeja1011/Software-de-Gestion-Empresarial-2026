<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index()
    {
        // withTrashed permite que el administrador consulte ventas anuladas.
        $sales = Sale::withTrashed()
            ->with(['client', 'paymentMethod', 'user'])
            ->latest('id')
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        return view('sales.create', [
            'clients' => Client::where('active', true)->orderBy('name')->get(),
            'methods' => PaymentMethod::where('active', true)->orderBy('name')->get(),
            'products' => Product::where('active', true)
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'stock', 'iva_percentage']),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleService $service)
    {
        $sale = $service->register(
            $request->validated(),
            $request->user()->id
        );

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', "Venta {$sale->invoice_number} registrada correctamente.");
    }

        public function pdf(Sale $sale)
    {
        $sale->load(['client', 'paymentMethod', 'user', 'details.product']);

        return Pdf::loadView('sales.pdf', compact('sale'))
            ->setPaper('letter')
            ->download(($sale->invoice_number ?? 'venta-'.$sale->id).'.pdf');
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'client',
            'paymentMethod',
            'user',
            'details.product',
        ]);

        return view('sales.show', compact('sale'));
    }

    /** Anula la venta y devuelve el stock, sin borrar su registro. */
    public function destroy(Sale $sale, InventoryService $inventoryService)
    {
        $anulada = DB::transaction(function () use ($sale, $inventoryService) {
            // Vuelve a leer y bloquea la venta para evitar anularla dos veces.
            $ventaBloqueada = Sale::query()
                ->lockForUpdate()
                ->findOrFail($sale->id);

            if (! $ventaBloqueada->active || $ventaBloqueada->status === 'anulada') {
                return false;
            }

            $ventaBloqueada->load('details');

            foreach ($ventaBloqueada->details as $detail) {
                $product = Product::withTrashed()
                    ->lockForUpdate()
                    ->find($detail->product_id);

                if ($product) {
                    $inventoryService->record(
                        $product,
                        'entrada',
                        (int) $detail->quantity,
                        'anulacion_venta',
                        $ventaBloqueada,
                        auth()->id(),
                        $product->cost_price !== null
                            ? (float) $product->cost_price
                            : null,
                        "Devolución de stock por anulación de {$ventaBloqueada->invoice_number}"
                    );
                }
            }

            $ventaBloqueada->update([
                'status' => 'anulada',
                'active' => false,
            ]);

            // Soft Delete: la venta queda guardada con deleted_at.
            $ventaBloqueada->delete();

            return true;
        });

        if (! $anulada) {
            return back()->withErrors([
                'sale' => 'Esta venta ya estaba anulada.',
            ]);
        }

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venta anulada y stock devuelto.');
    }
}