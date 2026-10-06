<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['client', 'paymentMethod'])->latest('id')->paginate(15);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        return view('sales.create', [
            'clients'  => Client::where('active', true)->orderBy('name')->get(),
            'methods'  => PaymentMethod::where('active', true)->orderBy('name')->get(),
            'products' => Product::where('active', true)->where('stock', '>', 0)->orderBy('name')
                            ->get(['id', 'name', 'price', 'stock']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'         => ['required', 'exists:clients,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'sale_date'         => ['required', 'date'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id', 'distinct'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $sale = DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'client_id'         => $data['client_id'],
                'payment_method_id' => $data['payment_method_id'],
                'sale_date'         => $data['sale_date'],
                'total'             => 0,
                'active'            => true,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para «{$product->name}» (disponible: {$product->stock}).",
                    ]);
                }

                $subtotal = round($product->price * $item['quantity'], 2);

                $sale->details()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                    'subtotal'   => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);
                $total += $subtotal;
            }

            $sale->update(['total' => $total]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Venta registrada.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['client', 'paymentMethod', 'details.product']);
        return view('sales.show', compact('sale'));
    }

    /** Anula la venta y devuelve el stock */
    public function destroy(Sale $sale)
    {
        if (! $sale->active) {
            return back()->withErrors('La venta ya estaba anulada.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->details as $detail) {
                Product::whereKey($detail->product_id)->increment('stock', $detail->quantity);
            }
            $sale->update(['active' => false]);
        });

        return back()->with('success', 'Venta anulada y stock devuelto.');
    }
}
