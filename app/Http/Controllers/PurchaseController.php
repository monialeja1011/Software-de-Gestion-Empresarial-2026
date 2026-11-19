<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['provider', 'paymentMethod'])
            ->latest('id')
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create', [
            'providers' => Provider::where('active', true)
                ->orderBy('name')
                ->get(),
            'methods' => PaymentMethod::where('active', true)
                ->orderBy('name')
                ->get(),
            'products' => Product::where('active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'cost_price', 'stock', 'iva_percentage']),
        ]);
    }

    /**
     * Valida la solicitud; PurchaseService crea la orden en una transacción.
     */
    public function store(Request $request, PurchaseService $purchaseService)
    {
        $data = $request->validate([
            'provider_id' => ['required', 'exists:providers,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'expected_delivery_date' => [
                'nullable',
                'date',
                'after_or_equal:purchase_date',
            ],
            'payment_term_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.iva_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.iva_included' => ['required', 'boolean'],
        ]);

        $purchase = $purchaseService->create($data);

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Orden de compra creada. El inventario no cambió.');
    }

    public function pdf(Purchase $purchase)
    {
        $purchase->load(['provider', 'paymentMethod', 'details.product']);

        $fileName = $purchase->order_number
            ?: 'OC-'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT);

        return Pdf::loadView('purchases.pdf', compact('purchase'))
            ->setPaper('letter')
            ->download($fileName.'.pdf');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load([
            'provider',
            'paymentMethod',
            'details.product',
            'receipts.user',
            'receipts.details.purchaseDetail.product',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function receive(
        Request $request,
        Purchase $purchase,
        PurchaseService $purchaseService
    ) {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity' => ['nullable', 'integer', 'min:0'],
            'observations' => ['nullable', 'string', 'max:5000'],
        ]);

        $purchaseService->receive(
            $purchase,
            $data,
            (int) $request->user()->id
        );

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Recepción registrada y stock actualizado.');
    }

    public function destroy(Purchase $purchase, PurchaseService $purchaseService)
    {
        $purchaseService->cancel($purchase);

        return back()->with('success', 'Orden de compra anulada.');
    }
}
