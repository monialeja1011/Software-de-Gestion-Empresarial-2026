<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public function index(Request $request)
{
    $filters = $request->validate([
        'buscar' => ['nullable', 'string', 'max:100'],
        'estado' => ['nullable', 'in:activa,inactiva'],
        'trashed' => ['nullable', 'boolean'],
    ]);

    $showTrashed = $request->boolean('trashed');
    $search = trim($filters['buscar'] ?? '');
    $estado = $filters['estado'] ?? '';

    if ($showTrashed) {
        abort_unless($request->user()->can('restaurar-categorias'), 403);
    }

    // Buscar o filtrar requiere el permiso buscar-categorias.
    if ($search !== '' || $estado !== '') {
        abort_unless($request->user()->can('buscar-categorias'), 403);
    }

    $query = Category::withCount([
        'products' => fn ($products) => $products->withTrashed(),
    ])->orderBy('name');

    if ($showTrashed) {
        $query->onlyTrashed();
    }

    if ($search !== '') {
        $query->where(function ($categories) use ($search) {
            $categories->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if ($estado === 'activa') {
        $query->where('active', true);
    } elseif ($estado === 'inactiva') {
        $query->where('active', false);
    }

    $categories = $query->paginate(15)->withQueryString();
    $trashedCount = Category::onlyTrashed()->count();

    return view('categories.index', compact(
        'categories',
        'showTrashed',
        'trashedCount',
        'search',
        'estado'
    ));
}

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->withTrashed()->exists()) {
            return back()->withErrors(
                'No se puede eliminar: la categoría tiene productos asociados.'
            );
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría enviada a la papelera.');
    }

    public function restore(string $category)
    {
        $trashedCategory = Category::onlyTrashed()->findOrFail($category);
        $trashedCategory->restore();

        return redirect()->route('categories.index', ['trashed' => 1])
            ->with('success', 'Categoría restaurada.');
    }
}