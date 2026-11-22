<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Importar productos desde Excel</h1>
            <p class="mt-1 text-sm text-slate-500">Los productos existentes conservan su stock actual.</p>
        </div>
    </x-slot>

    <x-ui.container>
        @if (session('import_summary'))
            <x-ui.card>
                <p class="font-semibold text-slate-800">
                    {{ session('import_summary')['created'] }} creados y
                    {{ session('import_summary')['updated'] }} actualizados.
                </p>
            </x-ui.card>
        @endif

        <x-ui.card>
            <div class="space-y-3 text-sm text-slate-600">
                <p>Descarga la plantilla y usa las columnas nombre, descripcion, precio, stock y categoria.</p>
                <p>La categoría debe existir y estar activa. Al importar un producto nuevo, el stock inicial queda registrado en el Kardex.</p>
                <a href="{{ route('products.template') }}" class="font-semibold text-pink-700 underline">Descargar plantilla</a>
            </div>

            <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="file" class="block text-sm font-medium text-slate-700">Archivo Excel o CSV, máximo 2 MB</label>
                    <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required class="mt-2 block w-full text-sm">
                    @error('file')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-lg bg-pink-600 px-4 py-2 font-semibold text-white">Importar productos</button>
                    <a href="{{ route('products.index') }}" class="rounded-lg border border-pink-200 px-4 py-2 text-slate-700">Volver a productos</a>
                </div>
            </form>
        </x-ui.card>

        @if (session('failures'))
            <x-ui.card>
                <h2 class="mb-4 font-semibold text-red-700">Filas omitidas por errores ({{ count(session('failures')) }})</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-pink-50 text-left">
                                <th class="p-3">Fila</th>
                                <th class="p-3">Columna</th>
                                <th class="p-3">Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('failures') as $failure)
                                <tr class="border-b border-pink-100">
                                    <td class="p-3">{{ $failure['fila'] }}</td>
                                    <td class="p-3">{{ $failure['columna'] }}</td>
                                    <td class="p-3">{{ implode(' ', $failure['errores']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </x-ui.container>
</x-app-layout>
