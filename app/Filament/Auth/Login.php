<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

/**
 * Halaman login panel admin dengan rate limit per kombinasi IP + email.
 *
 * Bawaan Filament v4 sudah membatasi 5 percobaan/menit ($this->rateLimit(5)
 * di authenticate()), tetapi kuncinya hanya per IP. Override kunci di bawah
 * menambahkan email yang diketik sehingga limit berlaku per IP + email.
 * Pesan "Terlalu banyak permintaan" sudah berbahasa Indonesia via
 * terjemahan bawaan Filament (APP_LOCALE=id).
 */
class Login extends BaseLogin
{
    protected function getRateLimitKey($method, $component = null)
    {
        $method ??= 'authenticate';
        $component ??= static::class;

        $email = mb_strtolower(trim((string) data_get($this->data, 'email', '')));

        return 'livewire-rate-limiter:'.sha1($component.'|'.$method.'|'.request()->ip().'|'.$email);
    }
}
