<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Editar producto</h1>
            <span class="text-sm text-slate-400">{{ $product->name }}</span>
        </div>
    </x-slot>

    <x-ui.container width="max-w-3xl">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <x-ui.card title="Datos del producto">
                @include('products._form')

                <x-slot name="footer">
                    <x-ui.button>Actualizar</x-ui.button>
                    <x-ui.link :href="route('products.index')">Cancelar</x-ui.link>
                </x-slot>
            </x-ui.card>
        </form>
    </x-ui.container>
</x-app-layout>
