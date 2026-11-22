<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                {{ $showTrashed ? 'Papelera de categorías' : 'Categorías' }}
            </h1>
            <span class="text-sm text-slate-400">Clasificación de productos</span>
        </div>
    </x-slot>

    <x-ui.container>
        @can('buscar-categorias')
            <x-ui.card>
                <form method="GET" action="{{ route('categories.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif

                    <x-ui.field name="buscar" label="Buscar categoría" type="search" :value="$search" placeholder="Nombre o descripción" class="lg:col-span-6" />

                    <x-ui.field as="select" name="estado" label="Estado" class="lg:col-span-3">
                        <option value="">Todos</option>
                        <option value="activa" @selected($estado === 'activa')>Activas</option>
                        <option value="inactiva" @selected($estado === 'inactiva')>Inactivas</option>
                    </x-ui.field>

                    <div class="flex items-center gap-3 lg:col-span-3">
                        <x-ui.button>Filtrar</x-ui.button>
                        <x-ui.link :href="route('categories.index', $showTrashed ? ['trashed' => 1] : [])">Limpiar</x-ui.link>
                    </div>
                </form>
            </x-ui.card>
        @endcan

        <x-ui.card flush title="Listado" :subtitle="$categories->total().' '.($categories->total() === 1 ? 'categoría' : 'categorías')">
            <x-slot name="actions">
                @if ($showTrashed)
                    <x-ui.button variant="secondary" :href="route('categories.index')">Ver categorías activas</x-ui.button>
                @else
                    @can('restaurar-categorias')
                        <x-ui.button variant="secondary" :href="route('categories.index', ['trashed' => 1])">Papelera ({{ $trashedCount }})</x-ui.button>
                    @endcan
                    @can('crear-categorias')
                        <x-ui.button :href="route('categories.create')">Nueva categoría</x-ui.button>
                    @endcan
                @endif
            </x-slot>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Productos asociados</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="text-slate-400">{{ $category->id }}</td>
                            <td class="font-medium text-slate-800">{{ $category->name }}</td>
                            <td class="text-slate-600">{{ $category->products_count }}</td>
                            <td>
                                <x-ui.badge :tone="$category->active ? 'success' : 'neutral'">
                                    {{ $category->active ? 'Activa' : 'Inactiva' }}
                                </x-ui.badge>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-4">
                                    @if ($showTrashed)
                                        <form action="{{ route('categories.restore', $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-emerald-700 underline-offset-2 hover:underline">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-categorias')
                                            <x-ui.link :href="route('categories.edit', $category)">Editar</x-ui.link>
                                        @endcan
                                        @can('eliminar-categorias')
                                            <x-ui.delete-form :action="route('categories.destroy', $category)" confirm="¿Enviar esta categoría a la papelera?" />
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-ui.empty :colspan="5">
                            {{ $showTrashed ? 'La papelera está vacía.' : 'Aún no hay categorías.' }}
                        </x-ui.empty>
                    @endforelse
                </tbody>
            </x-ui.table>

            @if ($categories->hasPages())
                <div class="border-t border-pink-100 px-6">
                    {{ $categories->links() }}
                </div>
            @endif
        </x-ui.card>
    </x-ui.container>
</x-app-layout>
