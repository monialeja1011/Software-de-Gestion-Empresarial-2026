<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4">
            <h1 class="text-2xl font-bold mb-6">Medios de pago</h1>

            <form action="{{ route('payment-methods.store') }}" method="POST" class="flex gap-2 mb-6">
                @csrf
                <input type="text" name="name" placeholder="Nuevo medio de pago (ej: Efectivo)" class="flex-1 border rounded p-2" required>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Agregar</button>
            </form>

            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="border-b"><th class="p-3 text-left">Nombre</th><th class="p-3 text-left">Activo</th><th class="p-3 text-left">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($methods as $m)
                        <tr class="border-b">
                            <td class="p-3" colspan="2">
                                <form action="{{ route('payment-methods.update', $m) }}" method="POST" class="flex items-center gap-3">
                                    @csrf @method('PATCH')
                                    <input type="text" name="name" value="{{ $m->name }}" class="border rounded p-1 flex-1" required>
                                    <label class="flex items-center gap-1">
                                        <input type="hidden" name="active" value="0">
                                        <input type="checkbox" name="active" value="1" @checked($m->active)> Activo
                                    </label>
                                    <button class="text-blue-600">Guardar</button>
                                </form>
                            </td>
                            <td class="p-3">
                                <form action="{{ route('payment-methods.destroy', $m) }}" method="POST" onsubmit="return confirm('¿Eliminar este medio de pago?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-3 text-gray-500">Aún no hay medios de pago.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
