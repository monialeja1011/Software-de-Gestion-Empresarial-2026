<x-app-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-3">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">Identidad corporativa</h1>
            <span class="text-sm text-slate-400">Datos y marca de tu empresa</span>
        </div>
    </x-slot>

    @php
        $names = ['trade_name', 'legal_name', 'nit', 'slogan', 'address', 'city', 'phone', 'email', 'website', 'brand_color'];
        $values = collect($names)
            ->mapWithKeys(fn ($n) => [$n => old($n, $setting->{$n}) ?? ''])
            ->all();

        $fieldClass = 'mt-1 block w-full rounded-lg border-pink-100 bg-white text-sm text-slate-700 shadow-sm focus:border-pink-400 focus:ring-pink-200 disabled:bg-pink-50/50 disabled:text-slate-500';

        $sections = [
            'Datos de la empresa' => [
                ['trade_name', 'Nombre comercial', 'text', 'Nombre con el que te conocen tus clientes', true, 'sm:col-span-2'],
                ['legal_name', 'Razón social', 'text', 'Nombre legal registrado', false, 'sm:col-span-2'],
                ['nit', 'NIT', 'text', '900.123.456-7', false, ''],
                ['slogan', 'Eslogan', 'text', 'Una frase corta que te represente', false, ''],
            ],
            'Contacto' => [
                ['address', 'Dirección', 'text', 'Calle y número', false, 'sm:col-span-2'],
                ['city', 'Ciudad', 'text', '', false, ''],
                ['phone', 'Teléfono', 'tel', '', false, ''],
                ['email', 'Correo electrónico', 'email', 'contacto@empresa.com', false, ''],
                ['website', 'Sitio web (opcional)', 'text', 'www.empresa.com', false, ''],
            ],
        ];
    @endphp

    <div class="min-h-screen bg-pink-50 py-8">
        <form method="POST" action="{{ route('company-settings.update') }}" enctype="multipart/form-data"
              class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-5 lg:px-8"
              x-data="{
                  f: @js($values),
                  logo: @js($setting->logoUrl()),
                  removeLogo: {{ old('remove_logo') ? 'true' : 'false' }},
                  get color() { return /^#[0-9a-fA-F]{6}$/.test(this.f.brand_color) ? this.f.brand_color : '#db2777' },
                  get initials() {
                      const all = (this.f.trade_name || '').trim().split(/\s+/).filter(Boolean);
                      const long = all.filter(w => w.length > 2);
                      const words = long.length ? long : all;
                      if (words.length >= 2) return words.slice(0, 3).map(w => w[0]).join('').toUpperCase();
                      return (words[0] || '?').slice(0, 2).toUpperCase();
                  },
                  pick(event) {
                      const file = event.target.files[0];
                      if (file) { this.logo = URL.createObjectURL(file); this.removeLogo = false; }
                  }
              }">
            @csrf
            @method('PATCH')

            <div class="space-y-6 lg:col-span-3">
                @if (session('status'))
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-5 py-3 text-sm text-emerald-800" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @unless ($canEdit)
                    <div class="rounded-xl border border-pink-100 bg-white px-5 py-3 text-sm text-slate-600">
                        Puedes consultar estos datos, pero tu usuario no tiene permiso para modificarlos.
                    </div>
                @endunless

                <fieldset @disabled(! $canEdit) class="min-w-0 space-y-6">
                    @foreach ($sections as $title => $fields)
                        <section class="rounded-2xl border border-pink-100 bg-white">
                            <header class="border-b border-pink-100 px-6 py-4">
                                <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
                            </header>
                            <div class="grid gap-4 px-6 py-5 sm:grid-cols-2">
                                @foreach ($fields as [$name, $label, $type, $placeholder, $required, $span])
                                    <div class="{{ $span }}">
                                        <label for="{{ $name }}" class="text-sm font-medium text-slate-700">{{ $label }}</label>
                                        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
                                               x-model="f.{{ $name }}" placeholder="{{ $placeholder }}"
                                               @required($required) class="{{ $fieldClass }}">
                                        @error($name)
                                            <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <section class="rounded-2xl border border-pink-100 bg-white">
                        <header class="border-b border-pink-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Imagen de marca</h2>
                        </header>

                        <div class="space-y-6 px-6 py-5">
                            <div>
                                <span class="text-sm font-medium text-slate-700">Logo</span>
                                <div class="mt-2 flex items-center gap-4">
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-pink-100 bg-pink-50/40">
                                        <img x-show="logo && !removeLogo" :src="logo" alt="Logo de la empresa"
                                             class="max-h-full max-w-full object-contain">
                                        <span x-show="!logo || removeLogo" class="text-xs text-slate-400">Sin logo</span>
                                    </div>
                                    <div class="space-y-2">
                                        <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" @change="pick($event)"
                                               class="block text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-pink-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-pink-800 hover:file:bg-pink-100">
                                        <p class="text-xs text-slate-400">PNG, JPG o WebP. Máximo 2 MB.</p>
                                        <label x-show="logo" class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="remove_logo" value="1" x-model="removeLogo"
                                                   class="rounded border-pink-200 text-pink-700 focus:ring-pink-200">
                                            Quitar el logo actual
                                        </label>
                                    </div>
                                </div>
                                @error('logo')
                                    <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="brand_color" class="text-sm font-medium text-slate-700">Color principal</label>
                                <div class="mt-2 flex items-center gap-3">
                                    <input type="color" x-model="f.brand_color" aria-label="Selector de color"
                                           class="h-10 w-14 cursor-pointer rounded-lg border border-pink-100 bg-white p-1">
                                    <input id="brand_color" type="text" name="brand_color" x-model="f.brand_color"
                                           maxlength="7" class="{{ $fieldClass }} !mt-0 w-32 font-mono uppercase">
                                </div>
                                <p class="mt-2 text-xs text-slate-400">
                                    Se aplica a botones, enlaces y detalles de todo el sistema. Si el color es muy claro,
                                    el sistema lo oscurece un poco para que el texto blanco se lea bien.
                                </p>
                                @error('brand_color')
                                    <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>
                </fieldset>

                @if ($canEdit)
                    <div class="flex items-center gap-4">
                        <button type="submit"
                                class="rounded-lg bg-pink-700 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2">
                            Guardar cambios
                        </button>
                        <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 underline-offset-2 hover:text-pink-800 hover:underline">Cancelar</a>
                    </div>
                @endif
            </div>

            <aside class="lg:col-span-2">
                <div class="space-y-4 lg:sticky lg:top-6">
                    <div class="overflow-hidden rounded-2xl border border-pink-100 bg-white">
                        <div class="h-2" :style="{ backgroundColor: color }"></div>
                        <div class="px-6 py-5">
                            <p class="text-xs text-slate-400">Así se verá el encabezado de tus facturas y reportes</p>

                            <div class="mt-4 flex items-start gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl"
                                     :class="(logo && !removeLogo) ? 'border border-pink-100 bg-white' : ''"
                                     :style="(logo && !removeLogo) ? {} : { backgroundColor: color }">
                                    <img x-show="logo && !removeLogo" :src="logo" alt="" class="max-h-full max-w-full object-contain">
                                    <span x-show="!logo || removeLogo" class="text-lg font-semibold text-white" x-text="initials"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-lg font-semibold text-slate-900" x-text="f.trade_name || 'Nombre de tu empresa'"></p>
                                    <p class="text-sm text-slate-500" x-show="f.slogan" x-text="f.slogan"></p>
                                </div>
                            </div>

                            <div class="mt-4 space-y-0.5 border-t border-pink-100 pt-4 text-sm text-slate-500">
                                <p x-show="f.legal_name" x-text="f.legal_name"></p>
                                <p x-show="f.nit" x-text="'NIT ' + f.nit"></p>
                                <p x-show="f.address || f.city" x-text="[f.address, f.city].filter(Boolean).join(', ')"></p>
                                <p x-show="f.phone" x-text="f.phone"></p>
                                <p x-show="f.email" x-text="f.email"></p>
                                <p x-show="f.website" x-text="f.website"></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-pink-100 bg-white px-6 py-5">
                        <p class="text-xs text-slate-400">Así se verán los botones y etiquetas</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <button type="button" class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                                    :style="{ backgroundColor: color }">Guardar cambios</button>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold"
                                  :style="{ color: color, backgroundColor: color + '1a' }">Activo</span>
                            <span class="text-sm font-medium underline-offset-2" :style="{ color: color }">Ver detalle</span>
                        </div>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</x-app-layout>