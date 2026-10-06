<x-app-layout>
    <div class="py-8">
        <div class="max-w-lg mx-auto px-4">
            <h1 class="text-2xl font-bold mb-6">Nuevo Cliente</h1>
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf 
                @include('clients._form')
                <button class="bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
                <a href="{{ route('clients.index') }}" class="ms-3 text-gray-600">Cancelar</a>
            </form>
        </div>
    </div>
</x-app-layout>
