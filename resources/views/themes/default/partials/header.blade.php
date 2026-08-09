@php
    $siteName = $settings->site_name ?? config('app.name');
    $siteShortName = filled($settings->site_short_name ?? null) ? $settings->site_short_name : null;
    $navItems = $primaryMenu?->rootItems ?? collect();

    $headerSocials = array_filter([
        'Facebook' => [$settings->facebook ?? null, 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z'],
        'Instagram' => [$settings->instagram ?? null, 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069Zm0-2.163C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0Zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324ZM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881Z'],
        'YouTube' => [$settings->youtube ?? null, 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814ZM9.545 15.568V8.432L15.818 12l-6.273 3.568Z'],
        'Twitter / X' => [$settings->twitter ?? null, 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z'],
    ], fn ($item) => filled($item[0]));

    $isNavActive = function (string $url): bool {
        if ($url === '#' || $url === '') {
            return false;
        }

        $current = rtrim(url()->current(), '/');
        $target = rtrim(url($url), '/');
        $home = rtrim(route('home'), '/');

        if ($target === $home) {
            return $current === $target;
        }

        return $current === $target || str_starts_with($current.'/', $target.'/');
    };

    $navLinkClass = function (bool $active): string {
        $base = 'relative flex h-full items-center px-4 py-3 text-[13px] font-bold uppercase tracking-wide transition';

        return $active
            ? $base.' bg-primary-700 text-amber-300 after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:bg-amber-400'
            : $base.' text-white hover:bg-primary-700/80 hover:text-amber-200';
    };
@endphp

<header class="relative z-40">
    {{-- Topbar tipis: kontak + sosial + RSS --}}
    <div class="border-b border-white/10 bg-primary-900 text-primary-100">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-1.5 sm:px-6 lg:px-8">
            <ul class="flex min-w-0 items-center divide-x divide-white/15 text-[11px] leading-none sm:text-xs">
                @if(filled($settings->email ?? null))
                    <li class="hidden pr-3 lg:block">
                        <a href="mailto:{{ $settings->email }}" class="inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="size-3.5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                            <span class="truncate">{{ $settings->email }}</span>
                        </a>
                    </li>
                @endif
                @if(filled($settings->phone ?? null))
                    <li class="px-3 first:pl-0">
                        <a href="tel:{{ preg_replace('/\s+/', '', $settings->phone) }}" class="inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="size-3.5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd"/></svg>
                            {{ $settings->phone }}
                        </a>
                    </li>
                @endif
                <li class="hidden px-3 sm:block">
                    <a href="{{ route('feed') }}" class="inline-flex items-center gap-1.5 transition hover:text-white">
                        <svg class="size-3.5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M3.75 3a.75.75 0 0 0-.75.75v.5c0 .414.336.75.75.75H4c6.075 0 11 4.925 11 11v.25c0 .414.336.75.75.75h.5a.75.75 0 0 0 .75-.75V16C17 8.82 11.18 3 4 3h-.25Z"/><path d="M3 8.75A.75.75 0 0 1 3.75 8H4a8 8 0 0 1 8 8v.25a.75.75 0 0 1-.75.75h-.5a.75.75 0 0 1-.75-.75V16a6 6 0 0 0-6-6h-.25A.75.75 0 0 1 3 9.25v-.5ZM7 15a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                        RSS
                    </a>
                </li>
            </ul>

            <div class="flex shrink-0 items-center gap-2.5 sm:gap-3">
                @if($headerSocials !== [])
                    <div class="hidden items-center gap-1 sm:flex">
                        @foreach($headerSocials as $label => [$url, $path])
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}" title="{{ $label }}"
                                class="flex size-6 items-center justify-center rounded-sm text-primary-200 transition hover:bg-white/10 hover:text-amber-300">
                                <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg>
                            </a>
                        @endforeach
                    </div>
                    <span class="hidden h-3 w-px bg-white/20 sm:block" aria-hidden="true"></span>
                @endif
                <a href="{{ route('contact.show') }}"
                    class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-amber-400 transition hover:text-amber-300 sm:text-xs">
                    Hubungi Kami
                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Brand band: logo + identitas + pencarian --}}
    <div class="border-b border-primary-900/40 bg-primary-800 text-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-4 sm:px-6 md:flex-row md:items-center md:justify-between md:py-5 lg:px-8">
            <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-3.5 sm:gap-4">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ $siteShortName ?? $siteName }}"
                    class="size-[3.25rem] shrink-0 drop-shadow-sm transition group-hover:opacity-95 sm:size-[4.25rem]">
                <span class="min-w-0">
                    @if($siteShortName)
                        <span class="block font-heading text-lg font-bold uppercase leading-[1.1] tracking-wide text-amber-400 sm:text-xl lg:text-[1.65rem]">{{ $siteShortName }}</span>
                        <span class="mt-1 block truncate font-heading text-sm font-semibold leading-snug text-white sm:text-base">{{ $siteName }}</span>
                    @else
                        <span class="line-clamp-2 font-heading text-lg font-bold uppercase leading-[1.15] tracking-wide text-amber-400 sm:text-xl md:line-clamp-1 lg:text-[1.65rem]">{{ $siteName }}</span>
                    @endif
                    <span class="mt-1.5 block truncate text-xs tracking-wide text-primary-100 sm:text-sm">
                        @if(filled($settings->jenjang ?? null)){{ $settings->jenjang }}@endif
                        @if(filled($settings->npsn ?? null))
                            <span class="text-primary-300" aria-hidden="true"> &middot; </span>NPSN {{ $settings->npsn }}
                        @endif
                    </span>
                    @if(filled($settings->address ?? null))
                        <span class="mt-1 hidden truncate text-[11px] leading-snug text-primary-200/90 md:block">{{ $settings->address }}</span>
                    @endif
                </span>
            </a>

            <form action="{{ route('posts.search') }}" method="get" class="hidden w-full max-w-xs shrink-0 md:block" role="search">
                <label for="header-q" class="sr-only">Cari berita</label>
                <div class="relative">
                    <input type="search" id="header-q" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
                        class="h-10 w-full rounded-sm border border-white/20 bg-primary-900/40 py-2 pl-3.5 pr-10 text-sm text-white placeholder-primary-300 transition focus:border-amber-400 focus:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-amber-400/40">
                    <button type="submit" aria-label="Cari" class="absolute inset-y-0 right-0 flex items-center px-3 text-primary-200 transition hover:text-amber-400">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Navbar biru cerah, sticky + bayangan halus saat scroll --}}
    <nav
        x-data="{ mobileOpen: false, scrolled: false }"
        @scroll.window.throttle.50ms="scrolled = window.scrollY > 8"
        :class="scrolled ? 'shadow-md shadow-primary-950/25' : 'shadow-sm'"
        class="sticky top-0 z-40 border-b border-primary-800 bg-primary-600 transition-shadow duration-200"
        aria-label="Menu utama"
    >
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between lg:hidden">
                <span class="py-2.5 text-sm font-bold uppercase tracking-wide text-white">Menu</span>
                <button type="button" @click="mobileOpen = ! mobileOpen"
                    class="-mr-1.5 rounded-sm p-2.5 text-primary-100 transition hover:bg-primary-700 hover:text-white"
                    aria-label="Buka menu" :aria-expanded="mobileOpen">
                    <svg x-show="! mobileOpen" class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-cloak x-show="mobileOpen" class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="hidden items-stretch lg:flex">
                <div class="flex flex-1 items-stretch divide-x divide-white/10">
                @forelse($navItems as $item)
                    @php
                        $childActive = $item->children->contains(fn ($child) => $isNavActive($child->resolvedUrl()));
                        $itemActive = $item->children->isEmpty()
                            ? $isNavActive($item->resolvedUrl())
                            : $childActive;
                    @endphp
                    @if($item->children->isNotEmpty())
                        <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = ! open"
                                class="{{ $navLinkClass($itemActive) }} gap-1.5"
                                :aria-expanded="open">
                                {{ $item->title }}
                                <svg class="size-3.5 opacity-80 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-cloak x-show="open"
                                x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                                x-transition:leave="transition duration-100 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute left-0 top-full z-50 min-w-[15rem] overflow-hidden rounded-b-sm border border-t-0 border-primary-900/20 bg-primary-800 py-1.5 shadow-xl shadow-primary-950/40">
                                @foreach($item->children as $child)
                                    @php $childIsActive = $isNavActive($child->resolvedUrl()); @endphp
                                    <a href="{{ $child->resolvedUrl() }}" @if($child->target) target="{{ $child->target }}" @endif
                                        @if($childIsActive) aria-current="page" @endif
                                        class="block border-b border-white/10 px-4 py-2.5 text-[13px] font-semibold uppercase tracking-wide transition last:border-b-0 {{ $childIsActive ? 'bg-primary-600 text-amber-300' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->resolvedUrl() }}" @if($item->target) target="{{ $item->target }}" @endif
                            @if($itemActive) aria-current="page" @endif
                            class="{{ $navLinkClass($itemActive) }}">
                            {{ $item->title }}
                        </a>
                    @endif
                @empty
                    @foreach([
                        'Beranda' => route('home'),
                        'Berita' => route('posts.index'),
                        'Galeri' => route('gallery.index'),
                        'Unduhan' => route('downloads.index'),
                        'Kontak' => route('contact.show'),
                    ] as $label => $url)
                        @php $fallbackActive = $isNavActive($url); @endphp
                        <a href="{{ $url }}" @if($fallbackActive) aria-current="page" @endif
                            class="{{ $navLinkClass($fallbackActive) }}">{{ $label }}</a>
                    @endforeach
                @endforelse
                </div>

                <a href="{{ route('ppdb.index') }}"
                    class="ml-px flex shrink-0 items-center gap-1.5 bg-amber-400 px-4 py-3 text-[13px] font-bold uppercase tracking-wide text-primary-950 transition hover:bg-amber-300">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                    PPDB Online
                </a>
            </div>
        </div>

        {{-- Menu mobile --}}
        <div x-cloak x-show="mobileOpen" x-transition.opacity.duration.150ms
            class="max-h-[75vh] overflow-y-auto overscroll-contain border-t border-primary-700/80 bg-primary-700/30 px-3 pb-4 pt-3 lg:hidden">
            <a href="{{ route('ppdb.index') }}"
                class="mb-2 block rounded-sm bg-amber-400 px-3 py-3 text-center text-sm font-bold uppercase tracking-wide text-primary-950 shadow-sm transition hover:bg-amber-300">
                PPDB Online
            </a>

            <div class="overflow-hidden rounded-sm bg-primary-700/40">
            @forelse($navItems as $item)
                @php
                    $childActive = $item->children->contains(fn ($child) => $isNavActive($child->resolvedUrl()));
                    $itemActive = $item->children->isEmpty()
                        ? $isNavActive($item->resolvedUrl())
                        : $childActive;
                @endphp
                @if($item->children->isNotEmpty())
                    <div x-data="{ open: {{ $itemActive ? 'true' : 'false' }} }" class="border-b border-white/10 last:border-b-0">
                        <button type="button" @click="open = ! open"
                            class="flex w-full items-center justify-between px-3 py-3 text-sm font-bold uppercase tracking-wide transition {{ $itemActive ? 'text-amber-300' : 'text-white hover:bg-primary-700' }}">
                            {{ $item->title }}
                            <svg class="size-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div x-cloak x-show="open" class="border-l-2 border-amber-400/70 pb-1 pl-2 ml-3">
                            @foreach($item->children as $child)
                                @php $childIsActive = $isNavActive($child->resolvedUrl()); @endphp
                                <a href="{{ $child->resolvedUrl() }}" @if($child->target) target="{{ $child->target }}" @endif
                                    @if($childIsActive) aria-current="page" @endif
                                    class="block rounded-sm px-3 py-2.5 text-sm transition {{ $childIsActive ? 'bg-primary-700 font-semibold text-amber-300' : 'text-primary-100 hover:bg-primary-700 hover:text-white' }}">
                                    {{ $child->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->resolvedUrl() }}" @if($item->target) target="{{ $item->target }}" @endif
                        @if($itemActive) aria-current="page" @endif
                        class="block border-b border-white/10 px-3 py-3 text-sm font-bold uppercase tracking-wide transition last:border-b-0 {{ $itemActive ? 'bg-primary-700 text-amber-300' : 'text-white hover:bg-primary-700' }}">
                        {{ $item->title }}
                    </a>
                @endif
            @empty
                @foreach([
                    'Beranda' => route('home'),
                    'Berita' => route('posts.index'),
                    'Galeri' => route('gallery.index'),
                    'Unduhan' => route('downloads.index'),
                    'Kontak' => route('contact.show'),
                ] as $label => $url)
                    @php $fallbackActive = $isNavActive($url); @endphp
                    <a href="{{ $url }}" @if($fallbackActive) aria-current="page" @endif
                        class="block border-b border-white/10 px-3 py-3 text-sm font-bold uppercase tracking-wide transition last:border-b-0 {{ $fallbackActive ? 'bg-primary-700 text-amber-300' : 'text-white hover:bg-primary-700' }}">{{ $label }}</a>
                @endforeach
            @endforelse
            </div>

            <form action="{{ route('posts.search') }}" method="get" class="mt-3 flex gap-2" role="search">
                <label for="mobile-q" class="sr-only">Cari berita</label>
                <input type="search" id="mobile-q" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
                    class="h-11 w-full rounded-sm border border-white/10 bg-primary-800/60 px-3.5 text-sm text-white placeholder-primary-200 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <button type="submit" class="h-11 shrink-0 rounded-sm bg-amber-400 px-4 text-sm font-bold text-primary-950 transition hover:bg-amber-300">Cari</button>
            </form>
        </div>
    </nav>
</header>
