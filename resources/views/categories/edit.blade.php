<x-app-layout>
    <div class="py-12">
        <div class="max-w-lg mx-auto">

            <h1 class="text-2xl font-bold mb-6">Editar Categoría</h1>

            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <label class="block mb-3">
                    Nombre:
                    <input type="text" name="name" value="{{ $category->name }}"
                           class="w-full border rounded p-2" required>
                </label>

                <label class="block mb-3">
                    Descripción:
                    <textarea name="description" class="w-full border rounded p-2">
                        {{ $category->description }}
                    </textarea>
                </label>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Actualizar
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
