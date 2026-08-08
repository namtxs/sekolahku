{{-- Header halaman + breadcrumb. Variabel: $title, opsional $breadcrumbs (array label => url|null), $subtitle --}}
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-7 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-2.5 text-xs text-slate-500 sm:text-sm">
            <ol class="flex flex-wrap items-center gap-1.5">
                <li>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 transition hover:text-primary-700">
                        <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd"/></svg>
                        Beranda
                    </a>
                </li>
                @foreach($breadcrumbs ?? [] as $label => $url)
                    <li aria-hidden="true">
                        <svg class="size-3 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </li>
                    <li class="min-w-0">
                        @if($url)
                            <a href="{{ $url }}" class="transition hover:text-primary-700">{{ $label }}</a>
                        @else
                            {{-- Judul panjang dipotong satu baris agar breadcrumb tidak menggunung di mobile --}}
                            <span class="line-clamp-1 font-medium text-slate-700">{{ $label }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
        <div class="relative inline-block border-b-2 border-amber-400 pb-2 pr-6">
            <h1 class="font-heading text-xl font-bold tracking-tight text-primary-900 sm:text-2xl">{{ $title }}</h1>
            <span class="absolute -bottom-0.5 left-0 w-1/3 border-b-2 border-primary-600" aria-hidden="true"></span>
        </div>
        @if(filled($subtitle ?? null))
            <p class="mt-2.5 max-w-2xl text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
</section>
