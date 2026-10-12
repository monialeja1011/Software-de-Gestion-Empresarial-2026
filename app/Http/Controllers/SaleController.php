<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Support\Facades\DB;

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
                ->get(['id', 'name', 'price', 'stock']),
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
    public function destroy(Sale $sale)
    {
        $anulada = DB::transaction(function () use ($sale) {
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
                    $product->increment('stock', $detail->quantity);
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