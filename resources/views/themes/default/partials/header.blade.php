@php
    $siteName = $settings->site_name ?? config('app.name');
    $navItems = $primaryMenu?->rootItems ?? collect();
@endphp

<header class="relative z-40">
    {{-- Header biru tua: logo + identitas sekolah + kontak/pencarian --}}
    <div class="bg-primary-800 text-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-5 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 sm:gap-4">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ $siteName }}" class="size-14 shrink-0 sm:size-[70px]">
                <span class="min-w-0">
                    {{-- Di layar kecil nama boleh 2 baris agar tidak terpotong; desktop tetap 1 baris elipsis --}}
                    <span class="line-clamp-2 font-heading text-lg font-bold uppercase leading-tight tracking-wide text-amber-400 sm:text-xl md:line-clamp-1 lg:text-2xl">{{ $siteName }}</span>
                    <span class="mt-1 block truncate text-sm text-primary-100">
                        @if(filled($settings->jenjang ?? null)){{ $settings->jenjang }}@endif
                        @if(filled($settings->npsn ?? null)) &middot; NPSN {{ $settings->npsn }}@endif
                    </span>
                    @if(filled($settings->address ?? null))
                        <span class="mt-0.5 hidden truncate text-xs text-primary-200 md:block">{{ $settings->address }}</span>
                    @endif
                </span>
            </a>

            <div class="hidden shrink-0 flex-col items-end md:flex">
                {{-- Kontak singkat ala topbar referensi --}}
                <ul class="flex items-center gap-4 text-xs leading-none text-primary-100">
                    @if(filled($settings->email ?? null))
                        <li class="hidden lg:block">
                            <a href="mailto:{{ $settings->email }}" class="inline-flex items-center gap-1.5 hover:text-white">
                                <svg class="size-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                                {{ $settings->email }}
                            </a>
                        </li>
                    @endif
                    @if(filled($settings->phone ?? null))
                        <li>
                            <a href="tel:{{ preg_replace('/\s+/', '', $settings->phone) }}" class="inline-flex items-center gap-1.5 hover:text-white">
                                <svg class="size-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd"/></svg>
                                {{ $settings->phone }}
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('feed') }}" class="inline-flex items-center gap-1.5 hover:text-white">
                            <svg class="size-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M3.75 3a.75.75 0 0 0-.75.75v.5c0 .414.336.75.75.75H4c6.075 0 11 4.925 11 11v.25c0 .414.336.75.75.75h.5a.75.75 0 0 0 .75-.75V16C17 8.82 11.18 3 4 3h-.25Z"/><path d="M3 8.75A.75.75 0 0 1 3.75 8H4a8 8 0 0 1 8 8v.25a.75.75 0 0 1-.75.75h-.5a.75.75 0 0 1-.75-.75V16a6 6 0 0 0-6-6h-.25A.75.75 0 0 1 3 9.25v-.5ZM7 15a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                            RSS
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact.show') }}" class="inline-flex items-center gap-1.5 font-semibold text-amber-400 hover:text-amber-300">Hubungi Kami</a>
                    </li>
                </ul>

                {{-- Pencarian --}}
                <form action="{{ route('posts.search') }}" method="get" class="mt-2.5 w-72" role="search">
                    <label for="header-q" class="sr-only">Cari berita</label>
                    <div class="relative">
                        <input type="search" id="header-q" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
                            class="w-full rounded-sm border border-white/20 bg-white/10 py-1.5 pl-3 pr-9 text-sm text-white placeholder-primary-200 transition focus:border-amber-400 focus:bg-primary-900 focus:outline-none focus:ring-1 focus:ring-amber-400">
                        <button type="submit" aria-label="Cari" class="absolute inset-y-0 right-0 px-2.5 text-primary-200 transition hover:text-amber-400">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Navbar biru cerah, uppercase, sticky --}}
    <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-40 border-b border-primary-800 bg-primary-600 shadow-sm" aria-label="Menu utama">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between lg:hidden">
                <span class="py-2.5 text-sm font-bold uppercase tracking-wide text-white">Menu</span>
                <button type="button" @click="mobileOpen = ! mobileOpen"
                    class="-mr-2.5 rounded p-2.5 text-primary-100 hover:bg-primary-700 hover:text-white" aria-label="Buka menu" :aria-expanded="mobileOpen">
                    <svg x-show="! mobileOpen" class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-cloak x-show="mobileOpen" class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Menu desktop: item dipisah garis halus agar rapi --}}
            <div class="hidden items-stretch lg:flex">
                <div class="flex flex-1 items-stretch divide-x divide-white/10">
                @forelse($navItems as $item)
                    @if($item->children->isNotEmpty())
                        <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = ! open"
                                class="flex h-full items-center gap-1 px-4 py-3 text-[13px] font-bold uppercase tracking-wide text-white transition hover:bg-primary-700">
                                {{ $item->title }}
                                <svg class="size-3.5 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-cloak x-show="open"
                                x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                                x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute left-0 top-full z-50 w-64 bg-primary-800 py-1 shadow-lg shadow-primary-950/30">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->resolvedUrl() }}" @if($child->target) target="{{ $child->target }}" @endif
                                        class="block border-b border-white/10 px-4 py-2.5 text-[13px] font-semibold uppercase tracking-wide text-primary-100 transition last:border-b-0 hover:bg-primary-600 hover:text-white">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->resolvedUrl() }}" @if($item->target) target="{{ $item->target }}" @endif
                            class="flex items-center px-4 py-3 text-[13px] font-bold uppercase tracking-wide text-white transition hover:bg-primary-700">
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
                        <a href="{{ $url }}" class="flex items-center px-4 py-3 text-[13px] font-bold uppercase tracking-wide text-white transition hover:bg-primary-700">{{ $label }}</a>
                    @endforeach
                @endforelse
                </div>

                {{-- Tombol PPDB menonjol (amber) di ujung kanan navbar --}}
                <a href="{{ route('ppdb.index') }}"
                    class="flex shrink-0 items-center gap-1.5 bg-amber-400 px-5 py-3 text-[13px] font-bold uppercase tracking-wide text-primary-950 transition hover:bg-amber-300">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                    PPDB Online
                </a>
            </div>
        </div>

        {{-- Menu mobile: bisa discroll bila item banyak (navbar sticky membatasi tinggi viewport) --}}
        <div x-cloak x-show="mobileOpen" x-transition.opacity.duration.150ms class="max-h-[75vh] overflow-y-auto overscroll-contain border-t border-primary-700 bg-primary-600 px-4 pb-4 pt-2 lg:hidden">
            <a href="{{ route('ppdb.index') }}"
                class="mb-1 block rounded bg-amber-400 px-3 py-3 text-center text-sm font-bold uppercase tracking-wide text-primary-950 hover:bg-amber-300">
                PPDB Online
            </a>
            @forelse($navItems as $item)
                @if($item->children->isNotEmpty())
                    <div x-data="{ open: false }">
                        <button type="button" @click="open = ! open"
                            class="flex w-full items-center justify-between rounded px-3 py-3 text-sm font-bold uppercase tracking-wide text-white hover:bg-primary-700">
                            {{ $item->title }}
                            <svg class="size-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div x-cloak x-show="open" class="ml-3 border-l-2 border-primary-400 pl-2">
                            @foreach($item->children as $child)
                                <a href="{{ $child->resolvedUrl() }}" @if($child->target) target="{{ $child->target }}" @endif
                                    class="block rounded px-3 py-2.5 text-sm text-primary-100 hover:bg-primary-700 hover:text-white">
                                    {{ $child->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->resolvedUrl() }}" @if($item->target) target="{{ $item->target }}" @endif
                        class="block rounded px-3 py-3 text-sm font-bold uppercase tracking-wide text-white hover:bg-primary-700">
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
                    <a href="{{ $url }}" class="block rounded px-3 py-3 text-sm font-bold uppercase tracking-wide text-white hover:bg-primary-700">{{ $label }}</a>
                @endforeach
            @endforelse

            <form action="{{ route('posts.search') }}" method="get" class="mt-3 flex gap-2" role="search">
                <label for="mobile-q" class="sr-only">Cari berita</label>
                <input type="search" id="mobile-q" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
                    class="w-full rounded border-0 bg-primary-700 px-3 py-2.5 text-sm text-white placeholder-primary-200 focus:outline-none focus:ring-2 focus:ring-amber-400">
                <button type="submit" class="rounded bg-amber-400 px-4 py-2.5 text-sm font-bold text-primary-950 hover:bg-amber-300">Cari</button>
            </form>
        </div>
    </nav>
</header>
