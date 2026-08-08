<?php

namespace Database\Seeders;

use App\Models\Widget;
use Illuminate\Database\Seeder;

/**
 * Widget bawaan per area. Idempotent per area: area yang sudah punya
 * widget dilewati sehingga aman dijalankan ulang setelah update.
 */
class WidgetSeeder extends Seeder
{
    public function run(): void
    {
        // Sidebar berita/beranda: fokus navigasi konten, tanpa info kontak
        // (kontak sudah ada di header, footer, dan sidebar halaman statis).
        $this->seedArea('sidebar', [
            ['title' => 'Pencarian', 'type' => 'search'],
            ['title' => 'Berita Populer', 'type' => 'popular_posts', 'settings' => ['limit' => 5]],
            ['title' => 'Kategori', 'type' => 'categories'],
            ['title' => 'Tautan', 'type' => 'links', 'settings' => ['links' => [
                ['label' => 'Kemendikdasmen', 'url' => 'https://www.kemendikdasmen.go.id'],
                ['label' => 'Dapodik', 'url' => 'https://dapo.kemendikdasmen.go.id'],
                ['label' => 'Data Referensi NPSN', 'url' => 'https://referensi.data.kemendikdasmen.go.id'],
                ['label' => 'Rumah Pendidikan', 'url' => 'https://rumah.pendidikan.go.id'],
                ['label' => 'Portal SNPMB', 'url' => 'https://snpmb.bppp.kemdikbud.go.id'],
            ]]],
            ['title' => 'Arsip', 'type' => 'archive', 'settings' => ['limit' => 12]],
            ['title' => 'Tag', 'type' => 'tags', 'settings' => ['limit' => 20]],
        ]);

        // Sidebar halaman statis: sengaja pendek agar seimbang dengan
        // artikel institusional yang umumnya singkat.
        $this->seedArea('page_sidebar', [
            ['title' => 'Berita Terbaru', 'type' => 'recent_posts', 'settings' => ['limit' => 4]],
            ['title' => 'Hubungi Kami', 'type' => 'contact_info'],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $widgets
     */
    protected function seedArea(string $area, array $widgets): void
    {
        if (Widget::query()->where('area', $area)->exists()) {
            $this->command?->info("Widget area {$area} sudah ada, dilewati.");

            return;
        }

        foreach ($widgets as $index => $widget) {
            Widget::create($widget + [
                'area' => $area,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
