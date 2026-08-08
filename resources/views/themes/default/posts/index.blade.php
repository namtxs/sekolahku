@extends('themes.default.layouts.app')

@section('title', $title)

@push('meta')
    @include('themes.default.partials.meta-og', [
        'title' => $title,
        'description' => $description ?? ($settings->meta_description ?? ''),
    ])
@endpush

@section('content')
    {{-- Halaman turunan (kategori/tag/arsip/cari) menampilkan jejaknya sendiri di breadcrumb --}}
    @include('themes.default.partials.page-header', [
        'title' => $title,
        'subtitle' => $description ?? null,
        'breadcrumbs' => $title === 'Berita'
            ? ['Berita' => null]
            : ['Berita' => route('posts.index'), $title => null],
    ])

    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
        {{-- Daftar berita --}}
        <div class="lg:col-span-2">
            @if($posts->isNotEmpty())
                <div class="rounded-sm border border-slate-300 bg-white p-5 shadow-sm sm:p-6">
                    @foreach($posts as $post)
                        @include('themes.default.partials.post-row', ['post' => $post])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @elseif(filled($searchQuery ?? null))
                @include('themes.default.partials.empty-state', [
                    'title' => 'Tidak ada hasil untuk "'.$searchQuery.'".',
                    'message' => 'Coba kata kunci lain yang lebih umum, atau telusuri semua berita sekolah.',
                    'iconPath' => 'm15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    'actionUrl' => route('posts.index'),
                    'actionLabel' => 'Semua Berita',
                ])
            @else
                @include('themes.default.partials.empty-state', [
                    'title' => 'Belum ada berita di bagian ini.',
                    'message' => 'Berita akan tampil di sini setelah dipublikasikan.',
                    'actionUrl' => route('posts.index'),
                    'actionLabel' => 'Semua Berita',
                ])
            @endif
        </div>

        {{-- Sidebar: di mobile turun ke bawah konten dengan garis pemisah --}}
        <div class="border-t border-slate-300 pt-8 lg:border-t-0 lg:pt-0">
            @include('themes.default.partials.sidebar')
        </div>
    </div>
@endsection
