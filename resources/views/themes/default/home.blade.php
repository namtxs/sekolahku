@extends('themes.default.layouts.app')

@section('meta_description', $settings->meta_description ?? '')

@push('meta')
    @include('themes.default.partials.meta-og', ['url' => route('home')])
@endpush

@section('content')
    <div class="mx-auto max-w-6xl px-4 pb-12 pt-4 sm:px-6 lg:px-8">
        {{-- Slider banner: boxed di dalam kontainer ala website sekolah Indonesia --}}
        @if($banners->isNotEmpty())
            <section x-data="{
                    current: 0,
                    total: {{ $banners->count() }},
                    timer: null,
                    next() { this.current = (this.current + 1) % this.total },
                    prev() { this.current = (this.current - 1 + this.total) % this.total },
                    start() { if (this.total > 1) this.timer = setInterval(() => this.next(), 6000) },
                    stop() { clearInterval(this.timer) },
                }"
                x-init="start()" @mouseenter="stop()" @mouseleave="start()"
                class="relative overflow-hidden rounded-sm border border-slate-300 bg-primary-900 shadow-sm" aria-label="Banner">
                <div class="relative h-52 sm:h-72 lg:h-[400px]">
                    @foreach($banners as $index => $banner)
                        @php($image = $banner->getFirstMediaUrl('banner', 'large'))
                        <div x-cloak x-show="current === {{ $index }}"
                            x-transition:enter="transition duration-700 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute inset-0">
                            @if($image)
                                {{-- Slide pertama diprioritaskan (LCP), sisanya lazy --}}
                                <img src="{{ $image }}" alt="{{ $banner->title }}" class="size-full object-cover"
                                    @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                            @else
                                <div class="flex size-full items-center justify-center bg-primary-800">
                                    <img src="{{ asset('images/logo.svg') }}" alt="" class="size-28 opacity-30">
                                </div>
                            @endif
                            {{-- Caption bar bawah ala referensi --}}
                            <div class="absolute inset-x-0 bottom-0 hidden border-t border-white/60 bg-primary-800/75 px-6 py-4 text-center sm:block">
                                <h2 class="font-heading text-base font-bold text-white sm:text-lg">{{ $banner->title }}</h2>
                                @if(filled($banner->description))
                                    <p class="mx-auto mt-1 line-clamp-1 max-w-3xl text-xs text-primary-100 sm:text-sm">{{ $banner->description }}</p>
                                @endif
                                @if(filled($banner->url))
                                    <a href="{{ $banner->url }}" class="mt-1 inline-block text-xs font-bold uppercase tracking-wide text-amber-400 hover:text-amber-300">Selengkapnya &raquo;</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($banners->count() > 1)
                    {{-- Area sentuh panah diperbesar di layar sentuh; tampilan desktop tetap --}}
                    <button type="button" @click="prev()" aria-label="Banner sebelumnya"
                        class="absolute left-3 top-1/2 -translate-y-1/2 rounded-sm bg-primary-950/50 p-2.5 text-white transition hover:bg-primary-950/80 sm:p-2">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    </button>
                    <button type="button" @click="next()" aria-label="Banner berikutnya"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-sm bg-primary-950/50 p-2.5 text-white transition hover:bg-primary-950/80 sm:p-2">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </button>
                    <div class="absolute inset-x-0 top-1.5 flex items-center justify-center">
                        @foreach($banners as $index => $banner)
                            {{-- Titik kecil dibungkus tombol berpadding agar mudah di-tap --}}
                            <button type="button" @click="current = {{ $index }}" aria-label="Ke banner {{ $index + 1 }}" class="p-1.5">
                                <span class="block size-2.5 rounded-full border border-white/70 transition"
                                    :class="current === {{ $index }} ? 'bg-amber-400 border-amber-400' : 'bg-white/60 hover:bg-white'"></span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>
        @else
            {{-- Sapaan sederhana bila belum ada banner --}}
            <section class="rounded-sm border border-slate-300 bg-primary-800 text-white">
                <div class="flex flex-col items-center gap-5 px-4 py-14 text-center">
                    <img src="{{ asset('images/logo.svg') }}" alt="" class="size-20">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-400">Selamat datang di website resmi</p>
                        <h1 class="mt-2 font-heading text-2xl font-bold uppercase sm:text-3xl">{{ $settings->site_name ?? config('app.name') }}</h1>
                        @if(filled($settings->jenjang ?? null))
                            <p class="mt-2 text-sm text-primary-100">{{ $settings->jenjang }}</p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- Strip pengumuman berjalan: bar biru tua + label emas ala "Kutipan" referensi --}}
        @if($announcements->isNotEmpty())
            <section class="mt-4 flex items-stretch overflow-hidden rounded-sm bg-primary-800 shadow-sm" aria-label="Pengumuman terbaru">
                <span class="inline-flex shrink-0 items-center gap-1.5 bg-amber-400 px-3 py-2.5 text-xs font-bold uppercase tracking-wide text-primary-950">
                    <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13.92 3.845a19.362 19.362 0 0 1-6.3 1.98C6.765 5.942 5.89 6 5 6a4 4 0 0 0-.504 7.969 15.97 15.97 0 0 0 1.271 3.34c.397.771 1.342 1 2.05.59l.867-.5c.726-.419.94-1.32.588-2.02-.166-.33-.315-.666-.448-1.004 1.8.357 3.511.963 5.096 1.78A17.964 17.964 0 0 0 15 10c0-2.162-.381-4.235-1.08-6.155ZM15.243 3.097A19.456 19.456 0 0 1 16.5 10c0 2.43-.445 4.758-1.257 6.904l-.03.077a.75.75 0 0 0 1.401.537 20.903 20.903 0 0 0 1.312-5.745 2 2 0 0 0 0-3.546 20.902 20.902 0 0 0-1.312-5.745.75.75 0 0 0-1.4.537l.029.078Z"/></svg>
                    Pengumuman
                </span>
                <div class="relative flex-1 overflow-hidden">
                    <div class="animate-ticker flex h-full w-max items-center whitespace-nowrap">
                        @foreach([0, 1] as $copy)
                            @foreach($announcements as $announcement)
                                <a href="{{ route('posts.show', $announcement->slug) }}"
                                    class="mx-6 inline-flex items-center gap-2 text-sm text-primary-100 hover:text-white hover:underline"
                                    @if($copy === 1) aria-hidden="true" tabindex="-1" @endif>
                                    <span class="size-1.5 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
                                    {{ $announcement->title }}
                                    <span class="text-xs text-primary-300">({{ tanggal_id($announcement->published_at, 'j M Y') }})</span>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Konten 2 kolom: utama kiri + sidebar widget kanan --}}
        <div class="mt-6 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                {{-- Berita terbaru: daftar kartu horizontal ala website sekolah --}}
                @include('themes.default.partials.section-title', ['title' => 'Berita Terbaru'])

                @if($latestPosts->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($latestPosts as $post)
                            <article class="overflow-hidden rounded-sm border border-slate-300 bg-white shadow-sm transition hover:shadow-md md:flex">
                                @php($image = $post->getFirstMediaUrl('featured', 'medium'))
                                <a href="{{ route('posts.show', $post->slug) }}" class="block shrink-0 md:w-2/5">
                                    @if($image)
                                        <img src="{{ $image }}" alt="{{ $post->title }}" loading="lazy" class="aspect-[4/3] w-full object-cover md:h-full">
                                    @else
                                        <span class="flex aspect-[4/3] w-full items-center justify-center bg-slate-100 md:h-full">
                                            <img src="{{ asset('images/logo.svg') }}" alt="" class="size-14 opacity-40">
                                        </span>
                                    @endif
                                </a>
                                <div class="flex min-w-0 flex-1 flex-col p-4">
                                    <h3 class="font-heading text-base font-bold leading-snug text-slate-900">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="transition hover:text-primary-700">{{ $post->title }}</a>
                                    </h3>
                                    @if(filled($post->excerpt))
                                        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
                                    @endif
                                    <div class="mt-auto flex items-center justify-between gap-3 pt-3">
                                        <p class="text-xs text-slate-500">{{ tanggal_id($post->published_at) }} &middot; Oleh {{ $post->user->name ?? 'Administrator' }} &middot; Dilihat {{ number_format($post->views, 0, ',', '.') }} kali</p>
                                        <a href="{{ route('posts.show', $post->slug) }}" aria-label="Baca {{ $post->title }}"
                                            class="shrink-0 rounded-sm bg-primary-600 p-2 text-white transition hover:bg-primary-800 sm:p-1.5">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('posts.index') }}"
                            class="inline-flex items-center gap-2 rounded-sm bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-800">
                            Lihat Semua Berita
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                @else
                    @include('themes.default.partials.empty-state', [
                        'title' => 'Belum ada berita yang dipublikasikan.',
                        'message' => 'Berita dan pengumuman terbaru sekolah akan tampil di sini.',
                    ])
                @endif

                {{-- Galeri foto terbaru --}}
                <div class="mt-10">
                    @include('themes.default.partials.section-title', [
                        'title' => 'Foto Terbaru',
                        'link' => route('gallery.index'),
                    ])
                    @if($albums->isNotEmpty())
                        {{-- 2 kolom sejak mobile agar beranda tidak memanjang --}}
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            @foreach($albums->take(4) as $album)
                                @php($cover = $album->getFirstMediaUrl('photos', 'thumb'))
                                <a href="{{ route('gallery.show', $album->slug) }}"
                                    class="group flex flex-col overflow-hidden rounded-sm border border-slate-300 bg-white shadow-sm transition hover:shadow-md">
                                    <span class="block overflow-hidden p-2">
                                        @if($cover)
                                            <img src="{{ $cover }}" alt="{{ $album->name }}" loading="lazy"
                                                class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                                        @else
                                            <span class="flex aspect-[4/3] w-full items-center justify-center bg-slate-100">
                                                <img src="{{ asset('images/logo.svg') }}" alt="" class="size-12 opacity-40">
                                            </span>
                                        @endif
                                    </span>
                                    <span class="flex flex-1 flex-col px-4 pb-4 pt-1">
                                        <span class="line-clamp-2 font-heading text-sm font-bold text-slate-900 group-hover:text-primary-700">{{ $album->name }}</span>
                                        <span class="mt-1 text-xs text-slate-500">{{ $album->getMedia('photos')->count() }} foto</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        @include('themes.default.partials.empty-state', [
                            'title' => 'Belum ada album galeri.',
                            'message' => 'Dokumentasi kegiatan sekolah akan tampil di sini.',
                            'iconPath' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
                        ])
                    @endif
                </div>
            </div>

            {{-- Sidebar kanan: kartu sambutan kepala sekolah + widget dari database.
                 Di mobile turun ke bawah konten dengan garis pemisah. --}}
            <div class="space-y-8 border-t border-slate-300 pt-8 lg:border-t-0 lg:pt-0">
                @if(filled($settings->headmaster_greeting ?? null))
                    <div x-data="{ expanded: false }" class="rounded-sm border border-slate-300 bg-white shadow-sm" aria-label="Sambutan Kepala Sekolah">
                        @include('themes.default.partials.card-title', ['title' => 'Sambutan Kepala Sekolah'])
                        <div class="px-4 py-4">
                            {{-- Foto kecil proporsional di samping nama, bukan potret tinggi --}}
                            <div class="flex items-center gap-3.5">
                                <img src="{{ asset('images/kepala-sekolah.svg') }}" alt="Foto {{ $settings->headmaster_name }}"
                                    class="size-16 shrink-0 rounded-full border-2 border-primary-100 object-cover object-top shadow-sm">
                                <div class="min-w-0">
                                    <p class="font-heading text-sm font-bold uppercase leading-snug text-primary-800">{{ $settings->headmaster_name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Kepala Sekolah</p>
                                </div>
                            </div>
                            <p x-show="! expanded" class="mt-3 text-justify text-sm leading-relaxed text-slate-600">&ldquo;{{ $headmasterQuote }}&#8230;&rdquo;</p>
                            <div x-cloak x-show="expanded" class="prose prose-sm prose-slate mt-3 max-w-none text-justify prose-p:leading-relaxed">
                                {!! $settings->headmaster_greeting !!}
                            </div>
                        </div>
                        <div class="border-t border-slate-200 px-4 py-2.5 text-center">
                            <button type="button" @click="expanded = ! expanded"
                                class="text-xs font-bold uppercase tracking-wide text-primary-700 hover:text-primary-900"
                                x-text="expanded ? 'Tutup' : 'Selengkapnya'">Selengkapnya</button>
                        </div>
                    </div>
                @endif

                @include('themes.default.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
