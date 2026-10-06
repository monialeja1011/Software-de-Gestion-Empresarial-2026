<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-2xl font-bold mb-6">Usuarios y roles</h1>
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b"><th class="p-3 text-left">Nombre</th><th class="p-3 text-left">Email</th><th class="p-3 text-left">Rol</th></tr>
                </thead>
                <tbody>
                    @foreach ($users as $u)
                        <tr class="border-b">
                            <td class="p-3">{{ $u->name }}</td>
                            <td class="p-3">{{ $u->email }}</td>
                            <td class="p-3">
                                <form action="{{ route('users.update', $u) }}" method="POST" class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <select name="role" class="border rounded p-1">
                                        <option value="admin" @selected($u->role === 'admin')>Administrador</option>
                                        <option value="vendedor" @selected($u->role === 'vendedor')>Vendedor</option>
                                    </select>
                                    <button class="text-blue-600">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
