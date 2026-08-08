{{-- Paginasi tema: biru, rounded-sm, teks Indonesia. Dipakai sebagai Paginator::defaultView. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman">
        {{-- Mobile: tombol Sebelumnya/Selanjutnya besar --}}
        <div class="flex items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="rounded-sm border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-300">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="rounded-sm border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-primary-600 hover:text-primary-700">Sebelumnya</a>
            @endif

            <span class="text-sm text-slate-500">Hal. {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="rounded-sm border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-primary-600 hover:text-primary-700">Selanjutnya</a>
            @else
                <span class="rounded-sm border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-300">Selanjutnya</span>
            @endif
        </div>

        {{-- Desktop: info jumlah + nomor halaman --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">
            <p class="text-sm text-slate-500">
                Menampilkan
                <span class="font-semibold text-slate-700">{{ number_format($paginator->firstItem() ?? 0, 0, ',', '.') }}</span>&ndash;<span class="font-semibold text-slate-700">{{ number_format($paginator->lastItem() ?? 0, 0, ',', '.') }}</span>
                dari
                <span class="font-semibold text-slate-700">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
                data
            </p>

            <ul class="flex items-center gap-1.5">
                {{-- Panah sebelumnya --}}
                <li>
                    @if ($paginator->onFirstPage())
                        <span class="flex size-10 items-center justify-center rounded-sm border border-slate-200 bg-slate-50 text-slate-300" aria-hidden="true">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                            class="flex size-10 items-center justify-center rounded-sm border border-slate-300 bg-white text-slate-600 transition hover:border-primary-600 hover:text-primary-700">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                        </a>
                    @endif
                </li>

                {{-- Nomor halaman --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li><span class="flex size-10 items-center justify-center text-sm text-slate-400">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li>
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                        class="flex size-10 items-center justify-center rounded-sm bg-primary-600 text-sm font-bold text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" aria-label="Ke halaman {{ $page }}"
                                        class="flex size-10 items-center justify-center rounded-sm border border-slate-300 bg-white text-sm font-semibold text-slate-600 transition hover:border-primary-600 hover:text-primary-700">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                {{-- Panah selanjutnya --}}
                <li>
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman selanjutnya"
                            class="flex size-10 items-center justify-center rounded-sm border border-slate-300 bg-white text-slate-600 transition hover:border-primary-600 hover:text-primary-700">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    @else
                        <span class="flex size-10 items-center justify-center rounded-sm border border-slate-200 bg-slate-50 text-slate-300" aria-hidden="true">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </span>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
@endif
