<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Categorías</h1>
                <a href="{{ route('categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nueva categoría</a>
            </div>
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Productos</th>
                        <th class="p-3 text-left">Estado</th>
                        <th class="p-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                        <tr class="border-b">
                            <td class="p-3">{{ $cat->id }}</td>
                            <td class="p-3">{{ $cat->name }}</td>
                            <td class="p-3">{{ $cat->products_count }}</td>
                            <td class="p-3">{{ $cat->active ? 'Activa' : 'Inactiva' }}</td>
                            <td class="p-3">
                                <a href="{{ route('categories.edit', $cat) }}" class="text-blue-600">Editar</a>
                                <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 ms-3">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500">Aún no hay categorías.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
