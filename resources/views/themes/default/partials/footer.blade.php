@php
    $siteName = $settings->site_name ?? config('app.name');
    $siteShortName = filled($settings->site_short_name ?? null) ? $settings->site_short_name : null;
    $siteTagline = filled($settings->site_tagline ?? null) ? $settings->site_tagline : null;
    $footerLinks = $footerMenu?->rootItems ?? collect();

    // Ikon sosial bulat berwarna khas website sekolah (warna brand masing-masing).
    $footerSocials = array_filter([
        'Facebook' => [$settings->facebook ?? null, 'bg-[#3b5998]', 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z'],
        'Instagram' => [$settings->instagram ?? null, 'bg-amber-500', 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069Zm0-2.163C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0Zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324ZM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881Z'],
        'YouTube' => [$settings->youtube ?? null, 'bg-[#ef4e17]', 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814ZM9.545 15.568V8.432L15.818 12l-6.273 3.568Z'],
        'Twitter / X' => [$settings->twitter ?? null, 'bg-[#1da1f2]', 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z'],
    ], fn ($item) => filled($item[0]));
@endphp

<footer class="border-t-[3px] border-amber-400 bg-primary-800 text-sm text-primary-100">
    {{-- Footer utama: brand | kontak | tautan | sosial (4 kolom di lg) --}}
    <div class="mx-auto grid max-w-6xl gap-x-10 gap-y-10 px-4 py-11 sm:px-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-8 lg:px-8 lg:py-12">
        {{-- 1. Brand: logo + nama pendek + nama lengkap + tagline --}}
        <div class="sm:col-span-2 lg:col-span-1">
            <a href="{{ route('home') }}" class="group inline-flex items-start gap-3.5">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ $siteShortName ?? $siteName }}"
                    class="size-14 shrink-0 drop-shadow-sm transition group-hover:opacity-95 sm:size-16">
                <span class="min-w-0 pt-0.5">
                    @if($siteShortName)
                        <span class="block font-heading text-base font-bold uppercase leading-[1.15] tracking-wide text-amber-400 sm:text-lg">{{ $siteShortName }}</span>
                        <span class="mt-1 block font-heading text-sm font-semibold uppercase leading-snug tracking-wide text-white">{{ $siteName }}</span>
                    @else
                        <span class="block font-heading text-base font-bold uppercase leading-[1.15] tracking-wide text-amber-400 sm:text-lg">{{ $siteName }}</span>
                    @endif
                    @if($siteTagline)
                        <span class="mt-1.5 block text-xs italic leading-snug text-primary-200/90 sm:text-[13px]">{{ $siteTagline }}</span>
                    @endif
                </span>
            </a>
        </div>

        {{-- 2. Hubungi Kami --}}
        <div>
            <h3 class="relative mb-5 border-b-2 border-amber-400 pb-2.5 font-heading text-base font-bold text-white">
                Hubungi Kami
                <span class="absolute -bottom-0.5 left-0 w-12 border-b-2 border-white" aria-hidden="true"></span>
            </h3>
            <ul class="space-y-3.5 leading-relaxed">
                @if(filled($settings->address ?? null))
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-sm bg-primary-900/50 text-amber-400">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.112.02.31-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9.5A7 7 0 1 0 3 9.5c0 2.993 1.698 5.488 3.355 7.084a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 1.039.573l.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd"/></svg>
                        </span>
                        <span class="pt-0.5">{{ $settings->address }}</span>
                    </li>
                @endif
                @if(filled($settings->phone ?? null))
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-sm bg-primary-900/50 text-amber-400">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd"/></svg>
                        </span>
                        <a href="tel:{{ preg_replace('/\s+/', '', $settings->phone) }}" class="pt-0.5 transition hover:text-amber-300">{{ $settings->phone }}</a>
                    </li>
                @endif
                @if(filled($settings->email ?? null))
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-sm bg-primary-900/50 text-amber-400">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                        </span>
                        <a href="mailto:{{ $settings->email }}" class="break-all pt-0.5 transition hover:text-amber-300">{{ $settings->email }}</a>
                    </li>
                @endif
            </ul>
        </div>

        {{-- 3. Tautan (menu footer) --}}
        <div>
            <h3 class="relative mb-5 border-b-2 border-amber-400 pb-2.5 font-heading text-base font-bold text-white">
                Tautan
                <span class="absolute -bottom-0.5 left-0 w-12 border-b-2 border-white" aria-hidden="true"></span>
            </h3>
            @if($footerLinks->isNotEmpty())
                <ul class="space-y-2.5">
                    @foreach($footerLinks as $item)
                        <li>
                            <a href="{{ $item->resolvedUrl() }}" @if($item->target) target="{{ $item->target }}" @endif
                                class="inline-flex items-center gap-2 transition hover:text-amber-300">
                                <span class="text-amber-400" aria-hidden="true">&rsaquo;</span>
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="space-y-2.5">
                    <li><a href="{{ route('home') }}" class="inline-flex items-center gap-2 transition hover:text-amber-300"><span class="text-amber-400" aria-hidden="true">&rsaquo;</span> Beranda</a></li>
                    <li><a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 transition hover:text-amber-300"><span class="text-amber-400" aria-hidden="true">&rsaquo;</span> Berita</a></li>
                    <li><a href="{{ route('contact.show') }}" class="inline-flex items-center gap-2 transition hover:text-amber-300"><span class="text-amber-400" aria-hidden="true">&rsaquo;</span> Kontak</a></li>
                    <li><a href="{{ route('ppdb.index') }}" class="inline-flex items-center gap-2 transition hover:text-amber-300"><span class="text-amber-400" aria-hidden="true">&rsaquo;</span> PPDB</a></li>
                </ul>
            @endif
        </div>

        {{-- 4. Ikuti Kami (+ Tags jika ada) --}}
        <div>
            <h3 class="relative mb-5 border-b-2 border-amber-400 pb-2.5 font-heading text-base font-bold text-white">
                Ikuti Kami
                <span class="absolute -bottom-0.5 left-0 w-12 border-b-2 border-white" aria-hidden="true"></span>
            </h3>
            @if($footerSocials !== [])
                <div class="flex flex-wrap gap-2.5">
                    @foreach($footerSocials as $label => [$url, $bg, $path])
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}" title="{{ $label }}"
                            class="{{ $bg }} flex size-10 items-center justify-center rounded-full text-white shadow-sm transition hover:-translate-y-0.5 hover:opacity-90 hover:shadow-md">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-primary-200">Media sosial belum diatur.</p>
            @endif

            @if(! empty($footerTags))
                <div class="mt-7">
                    <h4 class="mb-3 text-xs font-bold uppercase tracking-wide text-amber-400">Tags</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($footerTags as $tag)
                            <a href="{{ $tag['url'] }}"
                                class="rounded-sm border border-white/25 bg-primary-900/25 px-2.5 py-1.5 text-[11px] font-medium uppercase leading-none tracking-wide text-primary-100 transition hover:border-amber-400 hover:bg-primary-900/50 hover:text-amber-300">
                                {{ $tag['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bar copyright --}}
    <div class="border-t border-white/20 bg-primary-900/40">
        <div class="bg-primary-600">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2.5 px-4 py-3.5 text-xs text-white sm:flex-row sm:gap-3 sm:px-6 lg:px-8">
                <p class="text-center leading-relaxed sm:text-left">
                    Copyright &copy; {{ now()->year }}
                    <a href="{{ route('home') }}" class="font-semibold transition hover:text-amber-300">{{ $siteName }}</a>.
                    All rights reserved.
                </p>
                <nav class="flex flex-wrap items-center justify-center gap-x-1 gap-y-1 text-primary-50" aria-label="Tautan footer">
                    <a href="{{ route('home') }}" class="px-1.5 py-0.5 transition hover:text-amber-300">Beranda</a>
                    <span class="text-white/35" aria-hidden="true">&middot;</span>
                    <a href="{{ route('contact.show') }}" class="px-1.5 py-0.5 transition hover:text-amber-300">Kontak</a>
                    <span class="text-white/35" aria-hidden="true">&middot;</span>
                    <a href="{{ route('ppdb.index') }}" class="px-1.5 py-0.5 transition hover:text-amber-300">PPDB</a>
                    <span class="text-white/35" aria-hidden="true">&middot;</span>
                    <a href="{{ route('sitemap') }}" class="px-1.5 py-0.5 transition hover:text-amber-300">Peta Situs</a>
                    <span class="text-white/35" aria-hidden="true">&middot;</span>
                    <a href="{{ route('feed') }}" class="px-1.5 py-0.5 transition hover:text-amber-300">RSS</a>
                </nav>
            </div>
        </div>
    </div>
</footer>
