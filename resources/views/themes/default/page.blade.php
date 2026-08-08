@extends('themes.default.layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description ?: ($page->excerpt ?: ($settings->meta_description ?? '')))

@push('meta')
    @include('themes.default.partials.meta-og', [
        'title' => $page->meta_title ?: $page->title,
        'description' => $page->meta_description ?: ($page->excerpt ?: strip_tags($page->body)),
        'image' => $page->getFirstMediaUrl('featured', 'large') ?: null,
        'url' => route('pages.show', $page->slug),
    ])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => $page->title,
        'breadcrumbs' => [$page->title => null],
    ])

    @php
        // Menu samping: item root bermenu (mis. "Profil") yang memuat halaman ini,
        // fallback ke item bermenu pertama.
        $currentUrl = url()->current();
        $rootItems = $primaryMenu?->rootItems ?? collect();
        $sideMenu = $rootItems
            ->filter(fn ($item) => $item->children->isNotEmpty())
            ->first(fn ($item) => $item->children->contains(fn ($child) => $child->resolvedUrl() === $currentUrl))
            ?? $rootItems->first(fn ($item) => $item->children->isNotEmpty());
    @endphp

    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="lg:col-span-2">
            <article class="rounded-sm border border-slate-300 bg-white shadow-sm">
                @php($image = $page->getFirstMediaUrl('featured', 'large'))
                @if($image)
                    {{-- Rasio tetap mencegah layout shift saat gambar dimuat --}}
                    <img src="{{ $image }}" alt="{{ $page->title }}" class="aspect-[21/9] max-h-[420px] w-full rounded-t object-cover">
                @endif
                <div class="prose prose-slate prose-article max-w-none p-5 prose-headings:font-heading prose-headings:tracking-tight sm:p-8">
                    {!! $page->body !!}
                </div>
                @if($page->updated_at)
                    <p class="border-t border-slate-200 px-5 py-3 text-xs text-slate-400 sm:px-8">
                        Terakhir diperbarui {{ tanggal_id($page->updated_at) }}
                    </p>
                @endif
            </article>

            {{-- Halaman Lainnya: navigasi antar halaman institusional --}}
            @if($otherPages->isNotEmpty())
                <section class="mt-10" aria-label="Halaman lainnya">
                    @include('themes.default.partials.section-title', ['title' => 'Halaman Lainnya'])
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($otherPages as $other)
                            <a href="{{ route('pages.show', $other->slug) }}"
                                class="group flex items-start gap-3 rounded-sm border border-slate-300 bg-white p-4 shadow-sm transition hover:border-primary-400 hover:shadow-md">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-sm bg-primary-50 text-primary-700 transition group-hover:bg-primary-600 group-hover:text-white">
                                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block font-heading text-sm font-bold text-slate-900 transition group-hover:text-primary-700">{{ $other->title }}</span>
                                    @if(filled($other->excerpt))
                                        <span class="mt-0.5 line-clamp-2 block text-xs text-slate-500">{{ $other->excerpt }}</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- CTA kontak ringkas --}}
            <section class="mt-10 flex flex-col items-start gap-4 rounded-sm border border-primary-200 bg-primary-50 p-6 sm:flex-row sm:items-center sm:justify-between"
                aria-label="Hubungi kami">
                <div>
                    <h2 class="font-heading text-base font-bold text-primary-900">Butuh informasi lain?</h2>
                    <p class="mt-1 text-sm text-slate-600">Tim kami siap membantu menjawab pertanyaan Anda seputar sekolah.</p>
                </div>
                <a href="{{ route('contact.show') }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-sm bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-800">
                    Hubungi Kami
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </section>

            {{-- Komentar: hanya bila diaktifkan per halaman dari panel admin --}}
            @include('themes.default.partials.comments', ['post' => $page, 'comments' => $comments])
        </div>

        {{-- Sidebar: di mobile turun ke bawah konten dengan garis pemisah --}}
        <div class="space-y-8 border-t border-slate-300 pt-8 lg:border-t-0 lg:pt-0">
            @if($sideMenu)
                <nav class="rounded-sm border border-slate-300 bg-white shadow-sm" aria-label="Menu halaman">
                    @include('themes.default.partials.card-title', ['title' => $sideMenu->title])
                    <ul class="p-2">
                        @foreach($sideMenu->children as $child)
                            @php($isActive = $child->resolvedUrl() === $currentUrl)
                            <li>
                                <a href="{{ $child->resolvedUrl() }}" @if($child->target) target="{{ $child->target }}" @endif
                                    class="flex items-center gap-2 rounded px-3 py-2.5 text-sm {{ $isActive ? 'bg-primary-50 font-semibold text-primary-900' : 'text-slate-700 hover:bg-slate-50 hover:text-primary-800' }}">
                                    <svg class="size-3 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                                    {{ $child->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            {{-- Widget area khusus halaman statis (lebih ringkas dari sidebar berita) --}}
            @include('themes.default.partials.page-sidebar')
        </div>
    </div>
@endsection
