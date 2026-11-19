<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $products = Product::query()
            ->with('category')
            ->where('active', true)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($products) use ($search) {
                    $products->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        abort_unless($product->active, 404);

        return new ProductResource($product->load('category'));
    }
}
