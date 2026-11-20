@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-between gap-4 py-3">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg border border-pink-100 bg-white px-4 py-2 text-sm font-medium text-slate-300">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="rounded-lg border border-pink-200 bg-white px-4 py-2 text-sm font-medium text-pink-800 transition hover:bg-pink-50">
                    Anterior
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="rounded-lg border border-pink-200 bg-white px-4 py-2 text-sm font-medium text-pink-800 transition hover:bg-pink-50">
                    Siguiente
                </a>
            @else
                <span class="rounded-lg border border-pink-100 bg-white px-4 py-2 text-sm font-medium text-slate-300">
                    Siguiente
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Mostrando
                <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
                de
                <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
                {{ $paginator->total() == 1 ? 'resultado' : 'resultados' }}
            </p>

            <div class="inline-flex items-center gap-1 rounded-xl border border-pink-100 bg-white p-1 shadow-sm">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Página anterior"
                          class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-300">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior"
                       class="flex h-9 w-9 items-center justify-center rounded-lg text-pink-700 transition hover:bg-pink-50">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="flex h-9 w-9 items-center justify-center text-sm text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-gradient-to-br from-pink-700 to-pink-600 px-3 text-sm font-semibold text-white shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="Ir a la página {{ $page }}"
                                   class="flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-medium text-slate-600 transition hover:bg-pink-50 hover:text-pink-800">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente"
                       class="flex h-9 w-9 items-center justify-center rounded-lg text-pink-700 transition hover:bg-pink-50">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Página siguiente"
                          class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-300">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif