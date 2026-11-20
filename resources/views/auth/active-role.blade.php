<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-slate-900">Selecciona tu perfil</h1>
        <p class="mt-1 text-sm text-slate-600">
            Elige el rol con el que deseas trabajar en esta sesión.
        </p>
    </div>

    <form method="POST" action="{{ route('active-role.store') }}" class="space-y-4">
        @csrf

        <div class="space-y-2">
            @foreach ($roles as $role)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-pink-400 hover:bg-pink-50 has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                    <input
                        type="radio"
                        name="role_id"
                        value="{{ $role->id }}"
                        @checked(old('role_id') == $role->id)
                        required
                        class="border-slate-300 text-pink-600 focus:ring-pink-500"
                    >
                    <span class="font-medium text-slate-800">
                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    </span>
                </label>
            @endforeach
        </div>

        @error('role_id')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button
            type="submit"
            class="w-full rounded-xl bg-pink-600 px-5 py-3 font-semibold text-white transition hover:bg-pink-700"
        >
            Continuar
        </button>
    </form>
</x-guest-layout>
