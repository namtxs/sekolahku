<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use App\View\Composers\SidebarComposer;
use App\View\Composers\ThemeDataComposer;
use Filament\Tables\Table;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginasi memakai view tema (biru, teks Indonesia).
        Paginator::defaultView('themes.default.partials.pagination');

        // Default tabel panel admin: 25 baris per halaman, opsi wajar.
        Table::configureUsing(function (Table $table): void {
            $table
                ->defaultPaginationPageOption(25)
                ->paginationPageOptions([10, 25, 50]);
        });

        // Super Admin melewati semua pemeriksaan permission.
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Model Activity milik vendor — policy tak terdeteksi otomatis.
        Gate::policy(Activity::class, ActivityPolicy::class);

        // Data global (settings + menu) untuk semua view tema publik.
        View::composer(['themes.*', 'errors::*'], ThemeDataComposer::class);

        // Data widget hanya untuk partial sidebar (area berita) dan
        // page-sidebar (area halaman statis).
        View::composer(['themes.*.partials.sidebar', 'themes.*.partials.page-sidebar'], SidebarComposer::class);
    }
}
