<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['provider', 'paymentMethod'])->latest('id')->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create', [
            'providers' => Provider::where('active', true)->orderBy('name')->get(),
            'methods'   => PaymentMethod::where('active', true)->orderBy('name')->get(),
            'products'  => Product::where('active', true)->orderBy('name')->get(['id', 'name', 'price', 'stock']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'provider_id'       => ['required', 'exists:providers,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'purchase_date'     => ['required', 'date'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id', 'distinct'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.price'      => ['required', 'numeric', 'min:0'],
        ]);

        $purchase = DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'provider_id'       => $data['provider_id'],
                'payment_method_id' => $data['payment_method_id'],
                'purchase_date'     => $data['purchase_date'],
                'total'             => 0,
                'active'            => true,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $subtotal = round($item['price'] * $item['quantity'], 2);

                $purchase->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $subtotal,
                ]);

                Product::whereKey($item['product_id'])->increment('stock', $item['quantity']);
                $total += $subtotal;
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Compra registrada y stock actualizado.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['provider', 'paymentMethod', 'details.product']);
        return view('purchases.show', compact('purchase'));
    }

    /** Anula la compra y descuenta el stock que había entrado */
    public function destroy(Purchase $purchase)
    {
        if (! $purchase->active) {
            return back()->withErrors('La compra ya estaba anulada.');
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->details as $detail) {
                $product = Product::lockForUpdate()->find($detail->product_id);

                if ($product->stock < $detail->quantity) {
                    throw ValidationException::withMessages([
                        'purchase' => "No se puede anular: ya se vendió parte del stock de «{$product->name}».",
                    ]);
                }

                $product->decrement('stock', $detail->quantity);
            }

            $purchase->update(['active' => false]);
        });

        return back()->with('success', 'Compra anulada y stock descontado.');
    }
}
