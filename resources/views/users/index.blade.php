<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-2xl font-bold mb-6">Usuarios y roles</h1>

            @if (session('success'))
                <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="w-full rounded bg-white shadow">
                <thead>
                    <tr class="border-b">
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Rol actual</th>
                        <th class="p-3 text-left">Cambiar rol</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $u)
                        @php($currentRole = $u->getRoleNames()->first())

                        <tr class="border-b">
                            <td class="p-3">{{ $u->name }}</td>
                            <td class="p-3">{{ $u->email }}</td>
                            <td class="p-3">{{ $u->getRoleNames()->join(', ') }}</td>
                            <td class="p-3">
                                @can('gestionar-roles')
                                    <form
                                        action="{{ route('users.update', $u) }}"
                                        method="POST"
                                        class="flex items-center gap-2"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select name="role" class="rounded border p-1">
                                            <option value="admin" @selected($currentRole === 'admin')>
                                                Administrador
                                            </option>
                                            <option value="vendedor" @selected($currentRole === 'vendedor')>
                                                Vendedor
                                            </option>
                                            <option value="superadmin" @selected($currentRole === 'superadmin')>
                                                Superadmin
                                            </option>
                                        </select>

                                        <button type="submit" class="text-blue-600">
                                            Guardar
                                        </button>
                                    </form>
                                @else
                                    <span>Sin permiso para cambiar roles</span>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>