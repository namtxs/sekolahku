@extends('themes.default.layouts.app')

@section('title', $album->name)
@section('meta_description', $album->description ?: ($settings->meta_description ?? ''))

@push('meta')
    @include('themes.default.partials.meta-og', [
        'title' => $album->name,
        'description' => $album->description ?: ($settings->meta_description ?? ''),
        'image' => $album->getFirstMediaUrl('photos', 'large') ?: null,
        'url' => route('gallery.show', $album->slug),
    ])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => $album->name,
        'subtitle' => $album->description,
        'breadcrumbs' => ['Galeri' => route('gallery.index'), $album->name => null],
    ])

    @php
        $photos = $album->getMedia('photos')->map(fn ($media) => [
            'thumb' => $media->getUrl('thumb'),
            'large' => $media->getUrl('large'),
            'name' => $media->name,
        ])->values();
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8"
        x-data="{ open: false, idx: 0, photos: @js($photos) }"
        @keydown.escape.window="open = false"
        @keydown.arrow-right.window="if (open) idx = (idx + 1) % photos.length"
        @keydown.arrow-left.window="if (open) idx = (idx - 1 + photos.length) % photos.length">

        @if($photos->isNotEmpty())
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                @foreach($photos as $index => $photo)
                    <button type="button" @click="idx = {{ $index }}; open = true"
                        class="group relative aspect-square overflow-hidden rounded-sm border border-slate-300 bg-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <img src="{{ $photo['thumb'] }}" alt="{{ $photo['name'] }}" loading="lazy"
                            class="size-full object-cover transition duration-300 group-hover:scale-105">
                        <span class="absolute inset-0 flex items-center justify-center bg-slate-950/0 transition group-hover:bg-slate-950/30">
                            <svg class="size-8 text-white opacity-0 transition group-hover:opacity-100" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6"/></svg>
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Lightbox --}}
            <div x-cloak x-show="open" x-transition.opacity.duration.200ms
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 p-4"
                @click.self="open = false" role="dialog" aria-modal="true" aria-label="Pratinjau foto">
                <button type="button" @click="open = false" aria-label="Tutup"
                    class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/25">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
                <button type="button" @click="idx = (idx - 1 + photos.length) % photos.length" aria-label="Foto sebelumnya"
                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-2.5 text-white transition hover:bg-white/25 sm:left-6">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </button>
                <button type="button" @click="idx = (idx + 1) % photos.length" aria-label="Foto berikutnya"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-2.5 text-white transition hover:bg-white/25 sm:right-6">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>
                <figure class="max-h-full max-w-5xl">
                    <img :src="photos[idx]?.large" :alt="photos[idx]?.name" class="max-h-[82vh] w-auto rounded object-contain shadow-2xl">
                    <figcaption class="mt-3 text-center text-sm text-slate-300">
                        <span x-text="photos[idx]?.name"></span>
                        <span class="text-slate-500" x-text="` (${idx + 1}/${photos.length})`"></span>
                    </figcaption>
                </figure>
            </div>
        @else
            @include('themes.default.partials.empty-state', [
                'title' => 'Album ini belum memiliki foto.',
                'message' => 'Foto kegiatan akan ditambahkan segera.',
                'iconPath' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
                'actionUrl' => route('gallery.index'),
                'actionLabel' => 'Lihat Album Lain',
            ])
        @endif
    </div>
@endsection
