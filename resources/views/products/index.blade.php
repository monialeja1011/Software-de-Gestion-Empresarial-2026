<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-bold">
                    {{ $showTrashed ? 'Papelera de productos' : 'Productos' }}
                </h1>

                <div class="flex flex-wrap gap-3">
                    @if ($showTrashed)
                        <a href="{{ route('products.index') }}"
                           class="rounded border px-4 py-2">
                            Ver productos activos
                        </a>
                    @else
                        @can('crear-productos')
                            <a href="{{ route('products.create') }}"
                               class="rounded bg-blue-600 px-4 py-2 text-white">
                                Nuevo producto
                            </a>
                        @endcan

                        @can('restaurar-productos')
                            <a href="{{ route('products.index', ['trashed' => 1]) }}"
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

            @can('buscar-productos')
                <form method="GET" action="{{ route('products.index') }}"
                      class="mb-4 flex flex-wrap items-end gap-3 rounded bg-white p-4 shadow">

                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif

                    <div>
                        <label for="buscar" class="mb-1 block text-sm font-medium">
                            Buscar producto
                        </label>
                        <input
                            id="buscar"
                            name="buscar"
                            type="search"
                            value="{{ $search }}"
                            placeholder="Nombre, descripción o categoría"
                            class="rounded border-gray-300"
                        >
                    </div>

                    <div>
                        <label for="categoria_id" class="mb-1 block text-sm font-medium">
                            Categoría
                        </label>
                        <select id="categoria_id" name="categoria_id"
                                class="rounded border-gray-300">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected((string) $categoryId === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="estado" class="mb-1 block text-sm font-medium">
                            Estado
                        </label>
                        <select id="estado" name="estado" class="rounded border-gray-300">
                            <option value="">Todos</option>
                            <option value="activo" @selected($estado === 'activo')>
                                Activos
                            </option>
                            <option value="inactivo" @selected($estado === 'inactivo')>
                                Inactivos
                            </option>
                        </select>
                    </div>

                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white">
                        Filtrar
                    </button>

                    <a href="{{ route('products.index', $showTrashed ? ['trashed' => 1] : []) }}"
                       class="rounded border px-4 py-2">
                        Limpiar
                    </a>
                </form>
            @endcan

            <div class="overflow-x-auto rounded bg-white shadow">
                <table class="w-full">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-3">Nombre</th>
                            <th class="p-3">Categoría</th>
                            <th class="p-3 text-right">Precio</th>
                            <th class="p-3 text-right">Stock</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-b">
                                <td class="p-3">{{ $product->name }}</td>
                                <td class="p-3">
                                    {{ $product->category?->name ?? 'Sin categoría' }}
                                </td>
                                <td class="p-3 text-right">
                                    ${{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right">{{ $product->stock }}</td>
                                <td class="p-3">
                                    {{ $product->active ? 'Activo' : 'Inactivo' }}
                                </td>

                                <td class="p-3">
                                    @if ($showTrashed)
                                        <form action="{{ route('products.restore', $product->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="text-green-700 underline">
                                                Restaurar
                                            </button>
                                        </form>
                                    @else
                                        @can('editar-productos')
                                            <a href="{{ route('products.edit', $product) }}"
                                               class="text-blue-700 underline">
                                                Editar
                                            </a>
                                        @endcan

                                        @can('eliminar-productos')
                                            <form action="{{ route('products.destroy', $product) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Enviar este producto a la papelera?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="ms-3 text-red-700 underline">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-slate-500">
                                    @if ($showTrashed)
                                        La papelera está vacía o no hay resultados para esos filtros.
                                    @else
                                        No hay productos o no se encontraron resultados.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>