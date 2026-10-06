<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'active'      => ['nullable', 'boolean'],
        ];
    }

    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['active'] = $request->boolean('active');
        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate($this->rules());
        $data['active'] = $request->boolean('active');
        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->withErrors('No se puede eliminar: la categoría tiene productos. Puedes desactivarla.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada.');
    }
}
