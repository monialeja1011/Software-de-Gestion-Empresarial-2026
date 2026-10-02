<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold mb-6">Categorías</h1>

            <a href="{{ route('categories.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
                Nueva Categoría
            </a>

            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Estado</th>
                        <th class="p-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $cat)
                        <tr class="border-b">
                            <td class="p-3">{{ $cat->id }}</td>
                            <td class="p-3">{{ $cat->name }}</td>
                            <td class="p-3">
                                {{ $cat->active ? 'Activo' : 'Inactivo' }}
                            </td>
                            <td class="p-3">
                                <a href="{{ route('categories.edit', $cat) }}"
                                   class="text-blue-600">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</x-app-layout>
