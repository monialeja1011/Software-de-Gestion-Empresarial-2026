<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
   public function index(Request $request)
{
    $filters = $request->validate([
        'buscar' => ['nullable', 'string', 'max:100'],
        'categoria_id' => ['nullable', 'integer', 'exists:categories,id'],
        'estado' => ['nullable', 'in:activo,inactivo'],
        'trashed' => ['nullable', 'boolean'],
    ]);

    $showTrashed = $request->boolean('trashed');
    $search = trim($filters['buscar'] ?? '');
    $categoryId = $filters['categoria_id'] ?? '';
    $estado = $filters['estado'] ?? '';

    if ($showTrashed) {
        abort_unless($request->user()->can('restaurar-productos'), 403);
    }

    if ($search !== '' || $categoryId !== '' || $estado !== '') {
        abort_unless($request->user()->can('buscar-productos'), 403);
    }

    $query = Product::with('category');

    if ($showTrashed) {
        $query->onlyTrashed();
    }

    if ($search !== '') {
        $query->where(function ($products) use ($search) {
            $products->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('category', function ($categories) use ($search) {
                    $categories->where('name', 'like', "%{$search}%");
                });
        });
    }

    if ($categoryId !== '') {
        $query->where('category_id', $categoryId);
    }

    if ($estado === 'activo') {
        $query->where('active', true);
    } elseif ($estado === 'inactivo') {
        $query->where('active', false);
    }

    $products = $query->orderBy('name')->paginate(15)->withQueryString();
    $trashedCount = Product::onlyTrashed()->count();
    $categories = Category::orderBy('name')->get();

    return view('products.index', compact(
        'products',
        'showTrashed',
        'trashedCount',
        'search',
        'categoryId',
        'estado',
        'categories'
    ));
}

    public function create()
    {
        $categories = Category::where('active', true)
            ->orderBy('name')
            ->get();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)
            ->orWhere('id', $product->category_id)
            ->orderBy('name')
            ->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto enviado a la papelera.');
    }

    public function restore(string $product)
    {
        $trashedProduct = Product::onlyTrashed()->findOrFail($product);
        $trashedProduct->restore();

        return redirect()->route('products.index', ['trashed' => 1])
            ->with('success', 'Producto restaurado.');
    }
}