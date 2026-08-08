{{-- Empty state seragam: ikon + pesan ramah + aksi opsional.
     Variabel: $title, opsional $message, $iconPath (path d Heroicons outline 24), $actionUrl, $actionLabel --}}
<div class="rounded-sm border border-dashed border-slate-300 bg-white p-10 text-center sm:p-14">
    <svg class="mx-auto size-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath ?? 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5' }}"/>
    </svg>
    <p class="mt-4 font-semibold text-slate-600">{{ $title }}</p>
    @if(filled($message ?? null))
        <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">{{ $message }}</p>
    @endif
    @if(filled($actionUrl ?? null))
        <a href="{{ $actionUrl }}"
            class="mt-5 inline-block rounded-sm bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-800">
            {{ $actionLabel ?? 'Kembali ke Beranda' }}
        </a>
    @endif
</div>
