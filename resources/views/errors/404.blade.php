@extends('themes.'.theme_name().'.layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
    <div class="mx-auto flex max-w-3xl flex-col items-center px-4 py-20 text-center sm:px-6 lg:px-8">
        <img src="{{ asset('images/logo.svg') }}" alt="" class="size-20 opacity-60">
        <p class="mt-6 font-heading text-7xl font-bold text-primary-900 sm:text-8xl">404</p>
        <span class="mt-4 h-1 w-16 rounded-full bg-amber-400" aria-hidden="true"></span>
        <h1 class="mt-3 font-heading text-xl font-bold text-slate-900 sm:text-2xl">Halaman Tidak Ditemukan</h1>
        <p class="mt-3 max-w-md text-sm text-slate-500">
            Maaf, halaman yang Anda cari tidak tersedia. Halaman mungkin telah dipindahkan atau dihapus.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}"
                class="rounded-sm bg-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-800">
                Kembali ke Beranda
            </a>
            <a href="{{ route('posts.index') }}"
                class="rounded-sm border border-slate-300 bg-white px-6 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Lihat Berita
            </a>
        </div>

        {{-- Pencarian: bantu pengunjung menemukan konten yang dimaksud --}}
        <form action="{{ route('posts.search') }}" method="get" class="mt-8 flex w-full max-w-sm">
            <label for="notfound-q" class="sr-only">Cari berita</label>
            <input type="search" id="notfound-q" name="q" placeholder="Cari berita..."
                class="w-full rounded-l-sm border border-r-0 border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
            <button type="submit" aria-label="Cari"
                class="rounded-r-sm bg-primary-600 px-4 text-white transition hover:bg-primary-800">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </button>
        </form>

        <button type="button" onclick="history.back()"
            class="mt-5 text-sm font-semibold text-primary-700 transition hover:text-primary-900">
            &larr; Kembali ke halaman sebelumnya
        </button>
    </div>
@endsection
