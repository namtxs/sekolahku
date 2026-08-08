<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Settings\GeneralSettings;
use Filament\FontProviders\BunnyFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Throwable;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName(function (): string {
                try {
                    return app(GeneralSettings::class)->site_name ?: 'Sekolahku';
                } catch (Throwable) {
                    // Settings belum tersedia (mis. saat instalasi awal).
                    return 'Sekolahku';
                }
            })
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('favicon.svg'))
            // Font sama dengan situs publik.
            ->font('Plus Jakarta Sans', provider: BunnyFontProvider::class)
            // Satu nuansa terang yang konsisten; sidebar sudah navy gelap.
            ->darkMode(false)
            // Blok identitas sekolah di puncak sidebar navy.
            ->renderHook(
                PanelsRenderHook::SIDEBAR_START,
                fn (): View => view('filament.sidebar-brand'),
            )
            // Branding kartu login (header bawaan disembunyikan via theme).
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): View => view('filament.login-brand'),
            )
            ->colors([
                // Palet biru persis identitas situs publik (app.css):
                // tombol #0f6fc7 (600), aksen gelap #014585 (800).
                'primary' => [
                    50 => '#eaf3ff',
                    100 => '#d5e7fb',
                    200 => '#b0d1f4',
                    300 => '#82b4e9',
                    400 => '#4c92dc',
                    500 => '#2379cf',
                    600 => '#0f6fc7',
                    700 => '#0a58a3',
                    800 => '#014585',
                    900 => '#023a6e',
                    950 => '#062b50',
                ],
            ])
            ->sidebarCollapsibleOnDesktop()
            // Sidebar ramping ala software kantor (default 20rem terlalu lebar).
            ->sidebarWidth('15rem')
            ->collapsedSidebarWidth('4rem')
            ->navigationGroups([
                'Konten',
                'Tampilan',
                'Lainnya',
                'Sistem',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
