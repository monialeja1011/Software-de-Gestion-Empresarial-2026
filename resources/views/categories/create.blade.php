<x-app-layout>
    <div class="py-12">
        <div class="max-w-lg mx-auto">

            <h1 class="text-2xl font-bold mb-6">Nueva Categoría</h1>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <label class="block mb-3">
                    Nombre:
                    <input type="text" name="name" class="w-full border rounded p-2" required>
                </label>

                <label class="block mb-3">
                    Descripción:
                    <textarea name="description" class="w-full border rounded p-2"></textarea>
                </label>

                <button class="bg-green-600 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
