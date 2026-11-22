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
        <form method="GET" action="{{ route('audit.index') }}" class="mb-5 grid gap-4 rounded-2xl border border-pink-100 bg-white p-5 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
            <div>
                <label for="module" class="block text-sm font-medium text-slate-600">Módulo</label>
                <select id="module" name="module" class="mt-1 w-full rounded-lg border-pink-100 bg-white text-sm">
                    <option value="">Todos</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module === 'default' ? 'General' : ucfirst($module) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="event" class="block text-sm font-medium text-slate-600">Evento</label>
                <select id="event" name="event" class="mt-1 w-full rounded-lg border-pink-100 bg-white text-sm">
                    <option value="">Todos</option>
                    @foreach ($events as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['event'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="user" class="block text-sm font-medium text-slate-600">Usuario</label>
                <select id="user" name="user" class="mt-1 w-full rounded-lg border-pink-100 bg-white text-sm">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((string) ($filters['user'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="block text-sm font-medium text-slate-600">Desde</label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="mt-1 w-full rounded-lg border-pink-100 text-sm">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-slate-600">Hasta</label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="mt-1 w-full rounded-lg border-pink-100 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-pink-600 px-4 py-2 text-sm font-semibold text-white">Filtrar</button>
                <a href="{{ route('audit.index') }}" class="rounded-lg border border-pink-200 px-4 py-2 text-sm text-slate-700">Limpiar</a>
            </div>
        </form>

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
