{{-- Blok identitas sekolah di puncak sidebar navy (render hook SIDEBAR_START). --}}
@php
    $settings = rescue(fn () => app(\App\Settings\GeneralSettings::class), report: false);
    $siteName = $settings?->site_name ?: 'Sekolahku';
    $jenjang = $settings?->jenjang;
@endphp

<a href="{{ url('/admin') }}" class="sk-sidebar-brand">
    <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ $siteName }}" class="sk-sidebar-brand-logo">
    <span class="sk-sidebar-brand-text">
        <span class="sk-sidebar-brand-name">{{ $siteName }}</span>
        <span class="sk-sidebar-brand-sub">Panel Administrasi{{ $jenjang && $jenjang !== 'lainnya' ? ' · ' . $jenjang : '' }}</span>
    </span>
</a>
