<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Banners\BannerResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Posts\PostResource;
use Filament\Widgets\Widget;

/**
 * Pintasan tugas harian operator sekolah di dashboard.
 */
class QuickActions extends Widget
{
    protected string $view = 'filament.widgets.quick-actions';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return static::actions() !== [];
    }

    /**
     * Daftar pintasan sesuai izin pengguna yang sedang login.
     *
     * @return array<int, array{label: string, description: string, icon: string, url: string}>
     */
    public static function actions(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $actions = [];

        if ($user->can('posts.create')) {
            $actions[] = [
                'label' => 'Tulis Berita',
                'description' => 'Bagikan kabar terbaru sekolah',
                'icon' => 'heroicon-o-pencil-square',
                'url' => PostResource::getUrl('create'),
            ];
        }

        if ($user->can('banners.create')) {
            $actions[] = [
                'label' => 'Unggah Banner',
                'description' => 'Perbarui slider beranda',
                'icon' => 'heroicon-o-photo',
                'url' => BannerResource::getUrl('create'),
            ];
        }

        if ($user->can('menus.view_any')) {
            $actions[] = [
                'label' => 'Kelola Menu',
                'description' => 'Atur navigasi situs publik',
                'icon' => 'heroicon-o-bars-3',
                'url' => MenuResource::getUrl(),
            ];
        }

        // Pesan kontak memakai modul permission settings (lihat ContactMessagePolicy).
        if ($user->can('settings.view_any')) {
            $actions[] = [
                'label' => 'Pesan Masuk',
                'description' => 'Baca pesan dari pengunjung',
                'icon' => 'heroicon-o-envelope',
                'url' => ContactMessageResource::getUrl(),
            ];
        }

        return $actions;
    }
}
