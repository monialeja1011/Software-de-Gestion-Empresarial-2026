<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <header>
            <p class="text-sm font-semibold uppercase tracking-widest text-pink-600">Configuración</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Roles y permisos</h1>
            <p class="mt-1 text-sm text-slate-600">Configura el acceso por módulo y revisa permisos individuales.</p>
        </header>

        @if ($errors->any())
            <div class="rounded-xl bg-rose-100 p-4 text-rose-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-lg shadow-pink-100">
            <div class="border-b border-pink-100 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Cuentas registradas</h2>
                <p class="mt-1 text-sm text-slate-600">Consulta usuarios y administra sus accesos.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-pink-50 text-slate-700">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Usuario</th>
                            <th class="px-5 py-3 font-semibold">Correo</th>
                            <th class="px-5 py-3 font-semibold">Roles</th>
                            <th class="px-5 py-3 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-t border-slate-100">
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $user->email }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($user->roles as $role)
                                            <span class="rounded-full bg-pink-50 px-2.5 py-1 text-xs font-medium text-pink-800">
                                                {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                            </span>
                                        @empty
                                            <span class="text-slate-400">Sin rol</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if (auth()->user()->hasRole('superadmin') && auth()->id() !== $user->id)
                                        <form method="POST"
                                              action="{{ route('users.destroy', $user) }}"
                                              data-confirm="¿Eliminar la cuenta de {{ $user->name }}? Sus ventas y movimientos se conservarán."
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">
                                                Eliminar cuenta
                                            </button>
                                        </form>
                                    @elseif (auth()->id() === $user->id)
                                        <span class="text-xs text-slate-400">Tu cuenta</span>
                                    @else
                                        <span class="text-xs text-slate-400">Solo superadmin</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <form method="GET" action="{{ route('users.index') }}"
              class="grid gap-4 rounded-2xl bg-white p-5 shadow-lg shadow-pink-100 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">
            <label class="text-sm font-medium text-slate-700">
                Módulo
                <select name="module" class="mt-1 w-full rounded-lg border-pink-200 focus:border-pink-500 focus:ring-pink-300">
                    <option value="todos" @selected($moduleFilter === 'todos')>Todos los módulos</option>
                    @foreach ($moduleLabels as $key => $label)
                        <option value="{{ $key }}" @selected($moduleFilter === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-medium text-slate-700">
                Ver como usuario
                <select name="user_id" class="mt-1 w-full rounded-lg border-pink-200 focus:border-pink-500 focus:ring-pink-300">
                    <option value="" @selected($selectedUser === null)>— Ninguno —</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected($selectedUser?->id === $user->id)>
                            {{ $user->name }} — {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button class="self-end rounded-lg bg-pink-600 px-6 py-2.5 font-semibold text-white shadow-md shadow-pink-200 transition hover:bg-pink-700">
                Aplicar
            </button>
        </form>

        <form method="POST" action="{{ route('users.permissions.update') }}"
              class="overflow-hidden rounded-2xl bg-white shadow-lg shadow-pink-100">
            @csrf
            @method('PATCH')
            <input type="hidden" name="user_id" value="{{ $selectedUser?->id }}">
            <input type="hidden" name="module" value="{{ $moduleFilter }}">

            <div class="flex flex-col gap-2 border-b border-pink-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Matriz de acceso</h2>
                    <p class="text-sm text-slate-600">
                        Los cambios se aplican al guardar. Usuario seleccionado:
                        <span class="font-medium text-pink-700">{{ $selectedUser?->name ?? 'Ninguno' }}</span>
                    </p>
                </div>
                <button class="rounded-lg bg-pink-600 px-5 py-2.5 font-semibold text-white shadow-md shadow-pink-200 transition hover:-translate-y-0.5 hover:bg-pink-700">
                    Guardar permisos
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead class="bg-pink-50 text-slate-700">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Permiso</th>
                            <th class="px-4 py-3 font-semibold">Acción</th>
                            @foreach ($roles as $role)
                                <th class="px-4 py-3 text-center font-semibold">{{ ucfirst($role->name) }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-center font-semibold">
                                Permiso directo<br>
                                <span class="font-normal">{{ $selectedUser?->name }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissionGroups as $module => $group)
                            @if ($group->isNotEmpty())
                                <tr class="bg-slate-100">
                                    <th colspan="{{ 3 + $roles->count() }}" class="px-4 py-2 font-semibold text-pink-800">
                                        {{ $moduleLabels[$module] }}
                                    </th>
                                </tr>
                                @foreach ($group as $permission)
                                    @php
                                        $prefix = \Illuminate\Support\Str::before($permission->name, '-');
                                        $action = $actions[$prefix] ?? ucfirst($prefix);
                                        $moduleHidden = $moduleFilter !== 'todos' && $moduleFilter !== $module;
                                    @endphp
                                    <tr class="{{ $moduleHidden ? 'hidden' : '' }} border-t border-slate-100 hover:bg-pink-50/70">
                                        <td class="px-4 py-2.5 font-medium text-slate-800">{{ $permission->name }}</td>
                                        <td class="px-4 py-2.5 text-slate-600">{{ $action }}</td>
                                        @foreach ($roles as $role)
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox"
                                                       name="role_permissions[{{ $role->id }}][]"
                                                       value="{{ $permission->id }}"
                                                       @checked($role->permissions->contains('id', $permission->id))
                                                       class="rounded border-pink-300 text-pink-600 focus:ring-pink-500">
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-2.5 text-center">
                                            @if ($selectedUser)
                                                <input type="checkbox"
                                                       name="direct_permissions[]"
                                                       value="{{ $permission->id }}"
                                                       @checked($selectedUser->permissions->contains('id', $permission->id))
                                                       class="rounded border-pink-300 text-pink-600 focus:ring-pink-500">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-pink-100 p-5">
                <button class="rounded-lg bg-pink-600 px-5 py-2.5 font-semibold text-white shadow-md shadow-pink-200 transition hover:-translate-y-0.5 hover:bg-pink-700">
                    Guardar permisos
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
