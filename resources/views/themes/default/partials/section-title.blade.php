{{-- Judul section ala page-title website sekolah: underline emas + garis biru pendek. Variabel: $title, opsional $subtitle, $link, $linkLabel --}}
<div class="relative mb-5 flex flex-wrap items-end justify-between gap-3 border-b-2 border-amber-400 pb-2">
    <div>
        <h2 class="font-heading text-lg font-bold tracking-tight text-slate-800 sm:text-xl">{{ $title }}</h2>
        @if(filled($subtitle ?? null))
            <p class="mt-0.5 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if(filled($link ?? null))
        <a href="{{ $link }}" class="group inline-flex items-center gap-1 text-sm font-semibold text-primary-700 transition hover:text-primary-900">
            {{ $linkLabel ?? 'Lihat Semua' }}
            <svg class="size-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </a>
    @endif
    <span class="absolute -bottom-0.5 left-0 w-1/3 border-b-2 border-primary-600" aria-hidden="true"></span>
</div>
