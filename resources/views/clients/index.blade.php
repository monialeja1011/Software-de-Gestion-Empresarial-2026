<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Clientes</h1>
        </div>
    </x-slot>

    <x-ui.container>
        <x-ui.card flush title="Listado" :subtitle="$clients->total().' '.($clients->total() === 1 ? 'cliente' : 'clientes')">
            <x-slot name="actions">
                @can('crear-clientes')
                    <x-ui.button :href="route('clients.create')">Nuevo cliente</x-ui.button>
                @endcan
            </x-slot>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $item)
                        <tr>
                            <td class="font-medium text-slate-800">{{ $item->name }}</td>
                            <td class="tabular-nums text-slate-600">
                                @if ($item->document_number)
                                    {{ preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $item->document_number) }}
                                @else
                                    <x-ui.badge tone="warning">Pendiente</x-ui.badge>
                                @endif
                            </td>
                            <td class="text-slate-600">{{ $item->email ?: '—' }}</td>
                            <td class="text-slate-600">{{ $item->phone ?: '—' }}</td>
                            <td>
                                <x-ui.badge :tone="$item->active ? 'success' : 'neutral'">
                                    {{ $item->active ? 'Activo' : 'Inactivo' }}
                                </x-ui.badge>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-4">
                                    @can('editar-clientes')
                                        <x-ui.link :href="route('clients.edit', $item)">Editar</x-ui.link>
                                    @endcan
                                    @can('eliminar-clientes')
                                        <x-ui.delete-form :action="route('clients.destroy', $item)" confirm="¿Eliminar este cliente?" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-ui.empty :colspan="6">Aún no hay clientes registrados.</x-ui.empty>
                    @endforelse
                </tbody>
            </x-ui.table>

            @if ($clients->hasPages())
                <div class="border-t border-pink-100 px-6">
                    {{ $clients->links() }}
                </div>
            @endif
        </x-ui.card>
    </x-ui.container>
</x-app-layout>
