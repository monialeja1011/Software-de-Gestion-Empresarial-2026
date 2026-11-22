<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-slate-900">Notificaciones</h1>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.read-all') }}" class="flex justify-end">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-sm font-medium text-pink-700 hover:text-pink-900">
                        Marcar todas como leídas
                    </button>
                </form>
            @endif

            <section class="overflow-hidden rounded-2xl bg-white shadow">
                <ul class="divide-y divide-pink-100">
                    @forelse ($notifications as $notification)
                        <li class="flex items-start justify-between gap-4 px-5 py-4 {{ $notification->read_at ? 'opacity-60' : '' }}">
                            <div>
                                <p class="font-semibold text-slate-900">
                                    @unless ($notification->read_at)
                                        <span class="mr-1 inline-block h-2 w-2 rounded-full bg-pink-600"></span>
                                    @endunless
                                    {{ $notification->data['title'] ?? 'Notificación' }}
                                </p>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <p class="mt-2 text-xs text-slate-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm font-semibold text-pink-700 hover:text-pink-900">
                                    Abrir
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="px-5 py-10 text-center text-sm text-slate-500">
                            No tienes notificaciones.
                        </li>
                    @endforelse
                </ul>

                <div class="border-t border-pink-100 px-5 py-4">
                    {{ $notifications->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
