<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Proveedores</h1>
                <a href="{{ route('providers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nuevo proveedor</a>
            </div>
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Teléfono</th>
                        <th class="p-3 text-left">Estado</th>
                        <th class="p-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($providers as $item)
                        <tr class="border-b">
                            <td class="p-3">{{ $item->name }}</td>
                            <td class="p-3">{{ $item->email }}</td>
                            <td class="p-3">{{ $item->phone }}</td>
                            <td class="p-3">{{ $item->active ? 'Activo' : 'Inactivo' }}</td>
                            <td class="p-3">
                                <a href="{{ route('providers.edit', $item) }}" class="text-blue-600">Editar</a>
                                <form action="{{ route('providers.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este proveedor?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 ms-3">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500">Aún no hay registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $providers->links() }}</div>
        </div>
    </div>
</x-app-layout>
