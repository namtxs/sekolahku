@extends('themes.default.layouts.app')

@section('title', 'Galeri')

@push('meta')
    @include('themes.default.partials.meta-og', ['title' => 'Galeri'])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'Galeri Kegiatan',
        'subtitle' => 'Dokumentasi kegiatan dan momen berharga di sekolah.',
        'breadcrumbs' => ['Galeri' => null],
    ])

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @if($albums->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($albums as $album)
                    @php($cover = $album->getFirstMediaUrl('photos', 'medium'))
                    <a href="{{ route('gallery.show', $album->slug) }}"
                        class="group overflow-hidden rounded-sm border border-slate-300 bg-white shadow-sm transition hover:shadow-md">
                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $album->name }}" loading="lazy"
                                    class="size-full object-cover transition duration-300 group-hover:scale-[1.03]">
                            @else
                                <div class="flex size-full items-center justify-center bg-slate-100">
                                    <img src="{{ asset('images/logo.svg') }}" alt="" class="size-16 opacity-40">
                                </div>
                            @endif
                            <span class="absolute right-0 top-3 inline-flex items-center gap-1 bg-primary-800/85 px-2.5 py-1 text-xs font-semibold text-white">
                                <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M1 5.25A2.25 2.25 0 0 1 3.25 3h13.5A2.25 2.25 0 0 1 19 5.25v9.5A2.25 2.25 0 0 1 16.75 17H3.25A2.25 2.25 0 0 1 1 14.75v-9.5Zm1.5 5.81v3.69c0 .414.336.75.75.75h13.5a.75.75 0 0 0 .75-.75v-2.69l-2.22-2.219a.75.75 0 0 0-1.06 0l-1.91 1.909.47.47a.75.75 0 1 1-1.06 1.06l-6.97-6.97a.75.75 0 0 0-1.06 0l-1.19 1.19Z" clip-rule="evenodd"/></svg>
                                {{ $album->getMedia('photos')->count() }} foto
                            </span>
                        </div>
                        <div class="border-t-2 border-amber-400 p-4">
                            <h3 class="font-heading text-base font-bold leading-snug text-slate-900 group-hover:text-primary-800">{{ $album->name }}</h3>
                            @if(filled($album->description))
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $album->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $albums->links() }}
            </div>
        @else
            @include('themes.default.partials.empty-state', [
                'title' => 'Belum ada album galeri yang dipublikasikan.',
                'message' => 'Dokumentasi kegiatan dan momen sekolah akan tampil di sini.',
                'iconPath' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
                'actionUrl' => route('home'),
                'actionLabel' => 'Kembali ke Beranda',
            ])
        @endif
    </div>
@endsection
