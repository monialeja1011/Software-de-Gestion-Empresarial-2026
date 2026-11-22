<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Nuevo proveedor</h1>
            <span class="text-sm text-slate-400">Proveedores</span>
        </div>
    </x-slot>

    <x-ui.container width="max-w-2xl">
        <form action="{{ route('providers.store') }}" method="POST">
            @csrf
            <x-ui.card title="Datos del proveedor">
                @include('providers._form')

                <x-slot name="footer">
                    <x-ui.button>Guardar</x-ui.button>
                    <x-ui.link :href="route('providers.index')">Cancelar</x-ui.link>
                </x-slot>
            </x-ui.card>
        </form>
    </x-ui.container>
</x-app-layout>
