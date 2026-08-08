<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;

/**
 * Dashboard dengan sapaan personal sesuai jam (WIB) dan tanggal Indonesia.
 */
class Dashboard extends BaseDashboard
{
    public function getHeading(): string
    {
        // Timezone aplikasi UTC; sapaan mengikuti waktu Indonesia bagian barat.
        $hour = (int) Carbon::now('Asia/Jakarta')->format('G');

        $greeting = match (true) {
            $hour >= 4 && $hour < 11 => 'Selamat pagi',
            $hour >= 11 && $hour < 15 => 'Selamat siang',
            $hour >= 15 && $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $firstName = str(auth()->user()?->name ?? 'Operator')->before(' ');

        return "{$greeting}, {$firstName}!";
    }

    public function getSubheading(): ?string
    {
        return tanggal_id(Carbon::now('Asia/Jakarta'), 'l, j F Y');
    }
}
