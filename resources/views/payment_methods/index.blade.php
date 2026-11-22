<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Medios de pago</h1>
            <span class="text-sm text-slate-400">Formas de pago aceptadas en ventas y compras</span>
        </div>
    </x-slot>

    @php
        $control = 'block w-full rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-pink-400 focus:ring-pink-200';
    @endphp

    <x-ui.container width="max-w-3xl">
        @can('crear-metodos-pago')
            <x-ui.card title="Agregar medio de pago">
                <form action="{{ route('payment-methods.store') }}" method="POST" class="flex flex-wrap items-start gap-3">
                    @csrf
                    <div class="min-w-[14rem] flex-1">
                        <label for="new-method" class="sr-only">Nombre del medio de pago</label>
                        <input id="new-method" type="text" name="name" value="{{ old('name') }}" placeholder="Nuevo medio de pago (ej.: Efectivo)" class="{{ $control }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-ui.button>Agregar</x-ui.button>
                </form>
            </x-ui.card>
        @endcan

        <x-ui.card flush title="Medios registrados" :subtitle="$methods->count().' '.($methods->count() === 1 ? 'medio' : 'medios')">
            <ul>
                @forelse ($methods as $m)
                    <li class="flex flex-wrap items-center gap-3 border-t border-pink-50 px-6 py-4 first:border-t-0">
                        @can('editar-metodos-pago')
                            <form action="{{ route('payment-methods.update', $m) }}" method="POST" class="flex min-w-[16rem] flex-1 flex-wrap items-center gap-3">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="name" value="{{ $m->name }}" aria-label="Nombre del medio de pago" class="{{ $control }} min-w-[10rem] flex-1" required>
                                <x-ui.checkbox name="active" label="Activo" :checked="$m->active" />
                                <x-ui.button variant="secondary">Guardar</x-ui.button>
                            </form>
                        @else
                            <span class="flex-1 font-medium text-slate-800">{{ $m->name }}</span>
                            <x-ui.badge :tone="$m->active ? 'success' : 'neutral'">{{ $m->active ? 'Activo' : 'Inactivo' }}</x-ui.badge>
                        @endcan

                        @can('eliminar-metodos-pago')
                            <x-ui.delete-form :action="route('payment-methods.destroy', $m)" confirm="¿Eliminar este medio de pago?" />
                        @endcan
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-sm text-slate-500">Aún no hay medios de pago.</li>
                @endforelse
            </ul>
        </x-ui.card>
    </x-ui.container>
</x-app-layout>
