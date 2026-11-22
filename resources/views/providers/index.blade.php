<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Proveedores</h1>
            <span class="text-sm text-slate-400">Directorio de proveedores</span>
        </div>
    </x-slot>

    <x-ui.container>
        <x-ui.card flush title="Listado" :subtitle="$providers->total().' '.($providers->total() === 1 ? 'proveedor' : 'proveedores')">
            <x-slot name="actions">
                @can('crear-proveedores')
                    <x-ui.button :href="route('providers.create')">Nuevo proveedor</x-ui.button>
                @endcan
            </x-slot>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($providers as $item)
                        <tr>
                            <td class="font-medium text-slate-800">{{ $item->name }}</td>
                            <td class="text-slate-600">{{ $item->email ?: '—' }}</td>
                            <td class="text-slate-600">{{ $item->phone ?: '—' }}</td>
                            <td>
                                <x-ui.badge :tone="$item->active ? 'success' : 'neutral'">
                                    {{ $item->active ? 'Activo' : 'Inactivo' }}
                                </x-ui.badge>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-4">
                                    @can('editar-proveedores')
                                        <x-ui.link :href="route('providers.edit', $item)">Editar</x-ui.link>
                                    @endcan
                                    @can('eliminar-proveedores')
                                        <x-ui.delete-form :action="route('providers.destroy', $item)" confirm="¿Eliminar este proveedor?" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-ui.empty :colspan="5">Aún no hay proveedores registrados.</x-ui.empty>
                    @endforelse
                </tbody>
            </x-ui.table>

            @if ($providers->hasPages())
                <div class="border-t border-pink-100 px-6">
                    {{ $providers->links() }}
                </div>
            @endif
        </x-ui.card>
    </x-ui.container>
</x-app-layout>
