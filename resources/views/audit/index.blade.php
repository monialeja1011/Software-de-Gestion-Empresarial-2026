<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Auditoría del sistema</h1>
            <span class="text-sm text-slate-400">Quién hizo qué y cuándo</span>
        </div>
    </x-slot>

    @php
        $subjectNames = [
            'CompanySetting' => 'Identidad corporativa',
            'User' => 'Usuario',
            'Product' => 'Producto',
            'Category' => 'Categoría',
            'Client' => 'Cliente',
            'Provider' => 'Proveedor',
            'Sale' => 'Venta',
            'Purchase' => 'Compra',
            'PaymentMethod' => 'Medio de pago',
        ];

        $fieldNames = [
            'trade_name' => 'Nombre comercial',
            'legal_name' => 'Razón social',
            'nit' => 'NIT',
            'slogan' => 'Eslogan',
            'address' => 'Dirección',
            'city' => 'Ciudad',
            'phone' => 'Teléfono',
            'email' => 'Correo electrónico',
            'website' => 'Sitio web',
            'logo_path' => 'Logo',
            'brand_color' => 'Color principal',
        ];

        $show = fn ($value) => is_null($value) || $value === ''
            ? '—'
            : (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE));
    @endphp

    <x-ui.container>
        <x-ui.card flush title="Eventos registrados" :subtitle="$activities->total().' '.($activities->total() === 1 ? 'evento' : 'eventos')">
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Registro afectado</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        @php
                            $properties = $activity->properties->toArray();
                            $before = $properties['antes'] ?? null;
                            $after = $properties['despues'] ?? null;
                            $hasDiff = is_array($before) && is_array($after) && $after !== [];
                        @endphp
                        <tr class="align-top">
                            <td class="whitespace-nowrap !align-top text-slate-600">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="!align-top text-slate-800">{{ $activity->causer?->name ?? 'Sistema' }}</td>
                            <td class="!align-top font-medium text-slate-800">{{ $activity->description }}</td>
                            <td class="!align-top text-slate-600">
                                @if ($activity->subject)
                                    {{ $subjectNames[class_basename($activity->subject_type)] ?? class_basename($activity->subject_type) }}
                                    <span class="text-slate-400">#{{ $activity->subject_id }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="max-w-md !align-top">
                                @if ($hasDiff)
                                    <ul class="space-y-1 text-xs">
                                        @foreach ($after as $field => $new)
                                            <li>
                                                <span class="font-medium text-slate-700">{{ $fieldNames[$field] ?? $field }}:</span>
                                                <span class="text-rose-700 line-through">{{ $show($before[$field] ?? null) }}</span>
                                                <span class="text-slate-400">→</span>
                                                <span class="text-emerald-700">{{ $show($new) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <details class="{{ $hasDiff ? 'mt-2' : '' }}">
                                    <summary class="cursor-pointer text-xs font-medium text-pink-800 hover:underline">
                                        {{ $hasDiff ? 'Ver datos completos' : 'Ver detalle' }}
                                    </summary>
                                    <pre class="mt-2 whitespace-pre-wrap break-words rounded-lg bg-pink-50/60 p-3 text-xs text-slate-600">{{ json_encode(['cambios' => $activity->attribute_changes, 'detalle' => $properties], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <x-ui.empty :colspan="5">No hay eventos auditados todavía.</x-ui.empty>
                    @endforelse
                </tbody>
            </x-ui.table>

            @if ($activities->hasPages())
                <div class="border-t border-pink-100 px-6">
                    {{ $activities->links() }}
                </div>
            @endif
        </x-ui.card>
    </x-ui.container>
</x-app-layout>
