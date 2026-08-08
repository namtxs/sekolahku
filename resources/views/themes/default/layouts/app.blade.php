<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($siteName = $settings->site_name ?? config('app.name'))
    <title>@hasSection('title')@yield('title') &mdash; @endif{{ $siteName }}</title>
    <meta name="description" content="@yield('meta_description', $settings->meta_description ?? '')">
    @if(filled($settings->meta_keywords ?? null))
        <meta name="keywords" content="{{ $settings->meta_keywords }}">
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ $siteName }} &mdash; RSS" href="{{ route('feed') }}">
    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-primary-50 font-sans text-slate-800 antialiased">
    @include('themes.default.partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('themes.default.partials.footer')

    {{-- Tombol kembali ke atas: muncul setelah scroll cukup jauh --}}
    <div x-data="{ show: false }" @scroll.window.throttle.150ms="show = window.scrollY > 600">
        <button type="button" x-cloak x-show="show" x-transition.opacity.duration.200ms
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })" aria-label="Kembali ke atas"
            class="fixed bottom-5 right-5 z-40 rounded-sm border border-primary-700 bg-primary-600 p-2.5 text-white shadow-lg transition hover:bg-primary-800">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"/></svg>
        </button>
    </div>
</body>
</html>
