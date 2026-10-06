<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('name')->get();
        return view('payment_methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:80', 'unique:payment_methods,name']]);
        PaymentMethod::create($data + ['active' => true]);

        return back()->with('success', 'Medio de pago creado.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('payment_methods', 'name')->ignore($paymentMethod->id)],
        ]);
        $data['active'] = $request->boolean('active');
        $paymentMethod->update($data);

        return back()->with('success', 'Medio de pago actualizado.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->sales()->exists() || $paymentMethod->purchases()->exists()) {
            return back()->withErrors('No se puede eliminar: ya se usó en ventas o compras. Puedes desactivarlo.');
        }

        $paymentMethod->delete();

        return back()->with('success', 'Medio de pago eliminado.');
    }
}
