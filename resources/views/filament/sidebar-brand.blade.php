{{-- Blok identitas sekolah di puncak sidebar navy (render hook SIDEBAR_START). --}}
@php
    $settings = rescue(fn () => app(\App\Settings\GeneralSettings::class), report: false);
    $siteName = $settings?->site_name ?: 'Sekolahku';
    $siteShortName = filled($settings?->site_short_name) ? $settings->site_short_name : null;
    $brandPrimary = $siteShortName ?: $siteName;
    $jenjang = $settings?->jenjang;
@endphp

<a href="{{ url('/admin') }}" class="sk-sidebar-brand">
    <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ $brandPrimary }}" class="sk-sidebar-brand-logo">
    <span class="sk-sidebar-brand-text">
        <span class="sk-sidebar-brand-name">{{ $brandPrimary }}</span>
        <span class="sk-sidebar-brand-sub">
            @if($siteShortName)
                {{ $siteName }}
            @else
                Panel Administrasi{{ $jenjang && $jenjang !== 'lainnya' ? ' · '.$jenjang : '' }}
            @endif
        </span>
    </span>
</a>
