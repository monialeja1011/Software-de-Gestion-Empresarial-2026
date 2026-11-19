<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Auditoría del sistema</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-lg bg-white p-5 shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-pink-50">
                        <tr>
                            <th class="px-3 py-3 text-left">Fecha</th>
                            <th class="px-3 py-3 text-left">Usuario</th>
                            <th class="px-3 py-3 text-left">Acción</th>
                            <th class="px-3 py-3 text-left">Registro afectado</th>
                            <th class="px-3 py-3 text-left">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($activities as $activity)
                            <tr class="align-top">
                                <td class="whitespace-nowrap px-3 py-3">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-3 py-3">{{ $activity->causer?->name ?? 'Sistema' }}</td>
                                <td class="px-3 py-3">{{ $activity->description }}</td>
                                <td class="px-3 py-3">
                                    {{ $activity->subject ? class_basename($activity->subject_type).' #'.$activity->subject_id : '—' }}
                                </td>
                                <td class="max-w-md px-3 py-3">
                                    <pre class="whitespace-pre-wrap break-words text-xs">{{ json_encode(['cambios' => $activity->attribute_changes, 'detalle' => $activity->properties->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">No hay eventos auditados todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $activities->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
