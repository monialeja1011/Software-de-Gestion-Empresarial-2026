<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

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
     * Crea una orden de compra. Crear la orden no aumenta el inventario.
     */
    public function store(Request $request)
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

        $purchase = DB::transaction(function () use ($data) {
            $termDays = (int) ($data['payment_term_days'] ?? 0);

            $purchase = Purchase::create([
                'provider_id' => $data['provider_id'],
                'payment_method_id' => $data['payment_method_id'],
                'purchase_date' => $data['purchase_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'payment_term_days' => $termDays,
                'due_date' => $termDays > 0
                    ? now()->parse($data['purchase_date'])->addDays($termDays)->toDateString()
                    : $data['purchase_date'],
                'observations' => $data['observations'] ?? null,
                'total' => 0,
                'active' => true,
                'status' => 'enviada',
                'payment_status' => 'pendiente',
            ]);

            $purchase->update([
                'order_number' => 'OC-'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT),
            ]);

            $total = 0;

foreach ($data['items'] as $item) {
    $quantity = (int) $item['quantity'];
    $price = round((float) $item['price'], 2);
    $ivaPercentage = (float) $item['iva_percentage'];

    // Convierte correctamente el valor "0" o "1" que envía el formulario.
    $ivaIncluded = filter_var(
        $item['iva_included'],
        FILTER_VALIDATE_BOOLEAN
    );

    if ($ivaIncluded && $ivaPercentage > 0) {
        $baseUnitPrice = round(
            $price / (1 + ($ivaPercentage / 100)),
            2
        );
        $ivaUnitAmount = round($price - $baseUnitPrice, 2);
    } else {
        $baseUnitPrice = $price;
        $ivaUnitAmount = round(
            $price * ($ivaPercentage / 100),
            2
        );
    }

    $baseSubtotal = round($baseUnitPrice * $quantity, 2);
    $ivaAmount = round($ivaUnitAmount * $quantity, 2);
    $subtotal = round($baseSubtotal + $ivaAmount, 2);

    $purchase->details()->create([
        'product_id' => $item['product_id'],
        'quantity' => $quantity,
        'received_quantity' => 0,
        'price' => $price,
        'subtotal' => $subtotal,
        'iva_percentage' => $ivaPercentage,
        'iva_included' => $ivaIncluded,
        'base_subtotal' => $baseSubtotal,
        'iva_amount' => $ivaAmount,
    ]);

    $total += $subtotal;
}

            $purchase->update(['total' => $total]);

            return $purchase;
        });

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

    /**
     * Registra una recepción, total o parcial, y aumenta el stock recibido.
     */
    public function receive(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity' => ['nullable', 'integer', 'min:0'],
            'observations' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($request, $purchase, $data) {
            $lockedPurchase = Purchase::whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPurchase->status === 'anulada') {
                throw ValidationException::withMessages([
                    'purchase' => 'No se puede recibir una orden anulada.',
                ]);
            }

            $details = $lockedPurchase->details()
                ->lockForUpdate()
                ->get();

            $receivedItems = [];
            $receivedAnything = false;

            foreach ($details as $detail) {
                $quantity = (int) ($data['items'][$detail->id]['quantity'] ?? 0);

                if ($quantity === 0) {
                    continue;
                }

                $receivedAnything = true;
                $remaining = $detail->quantity - $detail->received_quantity;

                if ($quantity > $remaining) {
                    throw ValidationException::withMessages([
                        "items.{$detail->id}.quantity" =>
                            "La cantidad recibida supera lo pendiente para {$detail->product->name}. Pendiente: {$remaining}.",
                    ]);
                }

                $receivedItems[] = [
                    'detail' => $detail,
                    'quantity' => $quantity,
                ];
            }

            if (! $receivedAnything) {
                throw ValidationException::withMessages([
                    'items' => 'Indica una cantidad recibida mayor que cero.',
                ]);
            }

            $receipt = PurchaseReceipt::create([
                'purchase_id' => $lockedPurchase->id,
                'user_id' => $request->user()->id,
                'received_at' => now(),
                'observations' => $data['observations'] ?? null,
            ]);

            foreach ($receivedItems as $receivedItem) {
                $detail = $receivedItem['detail'];
                $quantity = $receivedItem['quantity'];

                $receipt->details()->create([
                    'purchase_detail_id' => $detail->id,
                    'quantity' => $quantity,
                ]);

                $detail->increment('received_quantity', $quantity);

                $product = Product::withTrashed()
                    ->whereKey($detail->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'purchase' => 'No se encontró uno de los productos de la orden.',
                    ]);
                }

                $product->increment('stock', $quantity);

// Guarda el costo recibido y calcula el precio de venta
// usando el porcentaje de recargo configurado para ese producto.
 // Calcula el costo unitario antes del IVA de compra.
$cost = $detail->quantity > 0
    ? round((float) $detail->base_subtotal / $detail->quantity, 2)
    : 0;

// Aplica el margen sobre el costo antes del IVA.
$markup = (float) $product->markup_percentage;
$salePriceBeforeIva = round(
    $cost * (1 + ($markup / 100)),
    2
);

// El precio público incluye el IVA de venta del producto.
$saleIvaPercentage = (float) $product->iva_percentage;
$publicPrice = round(
    $salePriceBeforeIva * (1 + ($saleIvaPercentage / 100)),
    2
);

$product->update([
    'cost_price' => $cost,
    'price' => $publicPrice,
]);
            }

            $allReceived = $lockedPurchase->details()
                ->whereColumn('received_quantity', '<', 'quantity')
                ->doesntExist();

            $lockedPurchase->update([
                'status' => $allReceived ? 'recibida' : 'parcialmente_recibida',
                'received_at' => $allReceived ? now() : null,
            ]);
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Recepción registrada y stock actualizado.');
    }

    /**
     * Anula una orden solo si todavía no se ha recibido ningún producto.
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'anulada') {
            return back()->withErrors([
                'purchase' => 'La orden ya estaba anulada.',
            ]);
        }

        if ($purchase->details()->where('received_quantity', '>', 0)->exists()) {
            return back()->withErrors([
                'purchase' => 'No se puede anular una orden que ya tiene productos recibidos.',
            ]);
        }

        $purchase->update([
            'active' => false,
            'status' => 'anulada',
        ]);

        return back()->with('success', 'Orden de compra anulada.');
    }
}