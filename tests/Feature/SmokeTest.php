<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

/**
 * Smoke test GET-only terhadap database utama yang sudah di-seed.
 *
 * PENTING: jangan tambahkan RefreshDatabase di sini — test ini sengaja
 * dijalankan terhadap database berisi konten demo (override env DB_* saat
 * menjalankan: DB_CONNECTION=mysql DB_DATABASE=sekolahku_v2 php artisan test).
 * Seluruh test hanya melakukan request GET tanpa mutasi data.
 */
class SmokeTest extends TestCase
{
    public function test_public_pages_return_successful_response(): void
    {
        foreach ([
            '/',
            '/berita',
            '/kategori/pengumuman',
            '/tag/lomba',
            '/cari?q=lomba',
            '/galeri',
            '/unduhan',
            '/kontak',
            '/profil-sekolah',
            '/sitemap.xml',
            '/feed',
        ] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_homepage_shows_site_identity(): void
    {
        $html = $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['SMK MUSAMBA', 'SMK Muhammadiyah 1 Ambarawa', 'Beriman, Berilmu, Berprestasi'])
            ->assertSee('Sambutan Kepala Sekolah')
            ->getContent();

        // Brand band: nama pendek + nama lengkap + tagline (NPSN/alamat tetap di footer).
        preg_match(
            '/<a href="[^"]*" class="group flex min-w-0 items-center[^"]*">.*?<\/a>/s',
            $html,
            $brandBand
        );
        $this->assertNotEmpty($brandBand, 'Header brand band markup tidak ditemukan');
        $this->assertStringContainsString('SMK MUSAMBA', $brandBand[0]);
        $this->assertStringContainsString('SMK Muhammadiyah 1 Ambarawa', $brandBand[0]);
        $this->assertStringContainsString('Beriman, Berilmu, Berprestasi', $brandBand[0]);
        $this->assertStringNotContainsString('NPSN', $brandBand[0]);
        $this->assertStringNotContainsString('Jl.', $brandBand[0]);
    }

    public function test_news_index_hides_unpublished_posts(): void
    {
        $this->get('/berita')
            ->assertOk()
            ->assertDontSee('Rencana Pembangunan Laboratorium')
            ->assertDontSee('Libur Nasional dan Cuti Bersama');
    }

    public function test_unknown_page_returns_not_found(): void
    {
        $this->get('/halaman-tidak-ada-xyz')->assertNotFound();
    }

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_dashboard_shows_stats_widgets(): void
    {
        $admin = User::where('email', 'admin@sekolahku-v2.test')->firstOrFail();

        // Widget Filament dimuat lazy via Livewire, sehingga label statistik
        // belum ada di HTML awal — cukup pastikan komponen widget ter-mount.
        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dasbor')
            ->assertSee('stats-overview')
            ->assertSee('posts-chart')
            ->assertSee('latest-pending-comments');
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect();
    }
}
