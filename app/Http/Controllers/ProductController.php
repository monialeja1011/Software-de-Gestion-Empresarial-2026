<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'active'      => ['nullable', 'boolean'],
        ];
    }

    public function index()
    {
        $products = Product::with('category')->orderBy('name')->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['active'] = $request->boolean('active');
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->orWhere('id', $product->category_id)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate($this->rules());
        $data['active'] = $request->boolean('active');
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleDetails()->exists() || $product->purchaseDetails()->exists()) {
            return back()->withErrors('No se puede eliminar: el producto tiene ventas o compras registradas. Puedes desactivarlo.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado.');
    }
}
