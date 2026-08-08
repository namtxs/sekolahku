<?php

namespace Tests\Feature;

use App\Filament\Auth\Login;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Uji rate limiting login panel admin: maksimal 5 percobaan per menit
 * per kombinasi IP + email. Tidak memakai DatabaseTransactions karena
 * hanya menyentuh rate limiter (cache array per test).
 */
class AdminLoginSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $component = Livewire::test(Login::class);

        foreach (range(1, 5) as $i) {
            $component
                ->fillForm([
                    'email' => 'admin@sekolahku-v2.test',
                    'password' => 'password-salah',
                ])
                ->call('authenticate')
                ->assertHasFormErrors(['email']);
        }

        // Percobaan ke-6: diblokir rate limiter (notifikasi Indonesia),
        // bukan pesan kredensial salah.
        $component
            ->fillForm([
                'email' => 'admin@sekolahku-v2.test',
                'password' => 'password-salah',
            ])
            ->call('authenticate')
            ->assertNotified();
    }

    public function test_rate_limit_key_is_scoped_per_email(): void
    {
        $component = Livewire::test(Login::class);

        foreach (range(1, 5) as $i) {
            $component
                ->fillForm([
                    'email' => 'penyerang@example.com',
                    'password' => 'password-salah',
                ])
                ->call('authenticate')
                ->assertHasFormErrors(['email']);
        }

        // Email lain dari IP sama masih dapat mencoba (limit per IP + email),
        // sehingga mendapat error kredensial biasa — bukan blokir limiter.
        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'pengguna-lain@example.com',
                'password' => 'password-salah',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);
    }
}
