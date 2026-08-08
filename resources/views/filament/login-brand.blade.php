{{-- Header navy bermerek di kartu login (render hook AUTH_LOGIN_FORM_BEFORE).
     Header bawaan Filament (.fi-simple-header) disembunyikan via theme.css. --}}
@php
    $settings = rescue(fn () => app(\App\Settings\GeneralSettings::class), report: false);
    $siteName = $settings?->site_name ?: 'Sekolahku';
@endphp

<div class="sk-login-brand">
    <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ $siteName }}" class="sk-login-brand-logo">
    <p class="sk-login-brand-name">{{ $siteName }}</p>
    <span class="sk-login-brand-rule" aria-hidden="true"></span>
    <p class="sk-login-brand-sub">Panel Administrasi Situs Sekolah</p>
</div>

<h1 class="sk-login-heading">Masuk ke Panel</h1>
<p class="sk-login-subheading">Gunakan akun yang diberikan oleh administrator sekolah.</p>
