<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Productos</h1>
                @if (Auth::user()->isAdmin())<a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nuevo producto</a>@endif
            </div>
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Categoría</th>
                        <th class="p-3 text-right">Precio</th>
                        <th class="p-3 text-right">Stock</th>
                        <th class="p-3 text-left">Estado</th>
                        @if (Auth::user()->isAdmin())<th class="p-3 text-left">Acciones</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $p)
                        <tr class="border-b">
                            <td class="p-3">{{ $p->name }}</td>
                            <td class="p-3">{{ $p->category->name }}</td>
                            <td class="p-3 text-right">${{ number_format($p->price, 0, ',', '.') }}</td>
                            <td class="p-3 text-right {{ $p->stock <= 5 ? 'text-red-600 font-semibold' : '' }}">{{ $p->stock }}</td>
                            <td class="p-3">{{ $p->active ? 'Activo' : 'Inactivo' }}</td>
                            @if (Auth::user()->isAdmin())
                            <td class="p-3">
                                <a href="{{ route('products.edit', $p) }}" class="text-blue-600">Editar</a>
                                <form action="{{ route('products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 ms-3">Eliminar</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-3 text-gray-500">Aún no hay productos.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
