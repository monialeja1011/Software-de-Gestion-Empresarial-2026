<x-app-layout>
    <div class="py-8">
        <div class="max-w-lg mx-auto px-4">
            <h1 class="text-2xl font-bold mb-6">Nuevo Producto</h1>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf 
                @include('products._form')
                <button class="bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
                <a href="{{ route('products.index') }}" class="ms-3 text-gray-600">Cancelar</a>
            </form>
        </div>
    </div>
</x-app-layout>
