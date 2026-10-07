<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-bold">
                    {{ $showTrashed ? 'Papelera de categorías' : 'Categorías' }}
                </h1>

                <div class="flex flex-wrap gap-3">
                    @if ($showTrashed)
                        <a href="{{ route('categories.index') }}"
                           class="rounded border px-4 py-2">
                            Ver categorías activas
                        </a>
                    @else
                        @can('crear-categorias')
                            <a href="{{ route('categories.create') }}"
                               class="rounded bg-blue-600 px-4 py-2 text-white">
                                Nueva categoría
                            </a>
                        @endcan

                        @can('restaurar-categorias')
                            <a href="{{ route('categories.index', ['trashed' => 1]) }}"
                               class="rounded border px-4 py-2">
                                Papelera ({{ $trashedCount }})
                            </a>
                        @endcan
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            @can('buscar-categorias')
    <form method="GET" action="{{ route('categories.index') }}"
          class="mb-4 flex flex-wrap items-end gap-3 rounded bg-white p-4 shadow">

        @if ($showTrashed)
            <input type="hidden" name="trashed" value="1">
        @endif

        <div>
            <label for="buscar" class="mb-1 block text-sm font-medium">
                Buscar categoría
            </label>
            <input
                id="buscar"
                name="buscar"
                type="search"
                value="{{ $search }}"
                placeholder="Nombre o descripción"
                class="rounded border-gray-300"
            >
        </div>

        <div>
            <label for="estado" class="mb-1 block text-sm font-medium">
                Estado
            </label>
            <select id="estado" name="estado" class="rounded border-gray-300">
                <option value="">Todos</option>
                <option value="activa" @selected($estado === 'activa')>Activas</option>
                <option value="inactiva" @selected($estado === 'inactiva')>Inactivas</option>
            </select>
        </div>

        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">
            Filtrar
        </button>

        <a href="{{ route('categories.index', $showTrashed ? ['trashed' => 1] : []) }}"
           class="rounded border px-4 py-2">
            Limpiar
        </a>
    </form>
@endcan
            <div class="overflow-x-auto rounded bg-white shadow">
                <table class="w-full">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-3">ID</th>
                            <th class="p-3">Nombre</th>
                            <th class="p-3">Productos asociados</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="border-b">
                                <td class="p-3">{{ $category->id }}</td>
                                <td class="p-3">{{ $category->name }}</td>
                                <td class="p-3">{{ $category->products_count }}</td>
                                <td class="p-3">
                                    {{ $category->active ? 'Activa' : 'Inactiva' }}
                                </td>
                                <td class="p-3">
                                    @if ($showTrashed)
                                        <form action="{{ route('categories.restore', $category->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">
                                                Restaurar
                                            </button>
                                        </form>
                                    @else
                                        @can('editar-categorias')
                                            <a href="{{ route('categories.edit', $category) }}"
                                               class="text-blue-700 underline">
                                                Editar
                                            </a>
                                        @endcan

                                        @can('eliminar-categorias')
                                            <form action="{{ route('categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Enviar esta categoría a la papelera?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ms-3 text-red-700 underline">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">
                                    {{ $showTrashed ? 'La papelera está vacía.' : 'Aún no hay categorías.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>