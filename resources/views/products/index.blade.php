<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                {{ $showTrashed ? 'Papelera de productos' : 'Productos' }}
            </h1>
        </div>
    </x-slot>

    <x-ui.container>
        @can('buscar-productos')
            <x-ui.card>
                <form method="GET" action="{{ route('products.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif

                    <x-ui.field name="buscar" label="Buscar producto" type="search" :value="$search" placeholder="Nombre, descripción o categoría" class="lg:col-span-5" />

                    <x-ui.field as="select" name="categoria_id" label="Categoría" class="lg:col-span-3">
                        <option value="">Todas</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </x-ui.field>

                    <x-ui.field as="select" name="estado" label="Estado" class="lg:col-span-2">
                        <option value="">Todos</option>
                        <option value="activo" @selected($estado === 'activo')>Activos</option>
                        <option value="inactivo" @selected($estado === 'inactivo')>Inactivos</option>
                    </x-ui.field>

                    <div class="flex items-center gap-3 lg:col-span-2">
                        <x-ui.button>Filtrar</x-ui.button>
                        <x-ui.link :href="route('products.index', $showTrashed ? ['trashed' => 1] : [])">Limpiar</x-ui.link>
                    </div>
                </form>
            </x-ui.card>
        @endcan

        <x-ui.card flush title="Listado" :subtitle="$products->total().' '.($products->total() === 1 ? 'producto' : 'productos')">
            <x-slot name="actions">
                @if ($showTrashed)
                    <x-ui.button variant="secondary" :href="route('products.index')">Ver productos activos</x-ui.button>
                @else
                    @can('restaurar-productos')
                        <x-ui.button variant="secondary" :href="route('products.index', ['trashed' => 1])">Papelera ({{ $trashedCount }})</x-ui.button>
                    @endcan
                    @can('exportar-productos')
                        <x-ui.button variant="secondary" :href="route('products.export', request()->only('buscar', 'categoria_id', 'estado'))">Exportar Excel</x-ui.button>
                    @endcan
                    @can('importar-productos')
                        <x-ui.button variant="secondary" :href="route('products.import')">Importar Excel</x-ui.button>
                    @endcan
                    @can('crear-productos')
                        <x-ui.button :href="route('products.create')">Nuevo producto</x-ui.button>
                    @endcan
                @endif
            </x-slot>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th class="text-right">Precio</th>
                        <th class="text-right">Stock</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="font-medium text-slate-800">{{ $product->name }}</td>
                            <td class="text-slate-600">{{ $product->category?->name ?? 'Sin categoría' }}</td>
                            <td class="text-right tabular-nums text-slate-700">${{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="text-right tabular-nums {{ $product->stock <= 5 ? 'font-semibold text-rose-700' : 'text-slate-700' }}">
                                {{ $product->stock }}
                            </td>
                            <td>
                                <x-ui.badge :tone="$product->active ? 'success' : 'neutral'">
                                    {{ $product->active ? 'Activo' : 'Inactivo' }}
                                </x-ui.badge>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-4">
                                    @if ($showTrashed)
                                        <form action="{{ route('products.restore', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-emerald-700 underline-offset-2 hover:underline">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-productos')
                                            <x-ui.link :href="route('products.edit', $product)">Editar</x-ui.link>
                                        @endcan
                                        @can('eliminar-productos')
                                            <x-ui.delete-form :action="route('products.destroy', $product)" confirm="¿Enviar este producto a la papelera?" />
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-ui.empty :colspan="6">
                            @if ($showTrashed)
                                La papelera está vacía o no hay resultados para esos filtros.
                            @else
                                No hay productos o no se encontraron resultados.
                            @endif
                        </x-ui.empty>
                    @endforelse
                </tbody>
            </x-ui.table>

            @if ($products->hasPages())
                <div class="border-t border-pink-100 px-6">
                    {{ $products->links() }}
                </div>
            @endif
        </x-ui.card>
    </x-ui.container>
</x-app-layout>
