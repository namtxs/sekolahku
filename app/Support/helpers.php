<?php

use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\View;

if (! function_exists('theme_name')) {
    /**
     * Nama tema aktif dari settings, dengan fallback 'default'
     * bila settings belum tersedia (mis. sebelum migrasi/seed).
     */
    function theme_name(): string
    {
        if (app()->bound('theme.active')) {
            return app('theme.active');
        }

        $theme = rescue(fn () => app(GeneralSettings::class)->active_theme, 'default', false) ?: 'default';

        app()->instance('theme.active', $theme);

        return $theme;
    }
}

if (! function_exists('theme_view')) {
    /**
     * Render view milik tema aktif: themes.{active_theme}.{view},
     * fallback ke tema default bila view tidak ada di tema aktif.
     */
    function theme_view(string $view, array $data = []): \Illuminate\Contracts\View\View
    {
        return View::first([
            'themes.'.theme_name().'.'.$view,
            'themes.default.'.$view,
        ], $data);
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal berbahasa Indonesia, mis. "7 Agustus 2026".
     */
    function tanggal_id(?\DateTimeInterface $date, string $format = 'j F Y'): string
    {
        if ($date === null) {
            return '';
        }

        return \Illuminate\Support\Carbon::instance($date)->locale('id')->translatedFormat($format);
    }
}
