<?php

namespace App\Support;

use App\Settings\GeneralSettings;
use Throwable;

/**
 * Helper identitas sekolah untuk email notifikasi.
 */
class SiteMail
{
    /**
     * Alamat email admin dari pengaturan umum; null bila belum diisi
     * (pengirim wajib men-skip pengiriman).
     */
    public static function adminEmail(): ?string
    {
        try {
            return app(GeneralSettings::class)->email ?: null;
        } catch (Throwable) {
            // Settings belum tersedia (mis. instalasi awal).
            return null;
        }
    }

    public static function siteName(): string
    {
        try {
            return app(GeneralSettings::class)->site_name ?: config('app.name');
        } catch (Throwable) {
            return config('app.name');
        }
    }
}
