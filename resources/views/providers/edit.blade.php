<x-app-layout>
    <div class="py-8">
        <div class="max-w-lg mx-auto px-4">
            <h1 class="text-2xl font-bold mb-6">Editar Proveedor</h1>
            <form action="{{ route('providers.update', $provider) }}" method="POST">
                @csrf @method('PUT')
                @include('providers._form')
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
                <a href="{{ route('providers.index') }}" class="ms-3 text-gray-600">Cancelar</a>
            </form>
        </div>
    </div>
</x-app-layout>
