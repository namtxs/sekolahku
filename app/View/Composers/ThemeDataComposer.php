<?php

namespace App\View\Composers;

use App\Models\Menu;
use App\Settings\GeneralSettings;
use Illuminate\View\View;

/**
 * Membagikan data global (settings + menu) ke seluruh view tema.
 */
class ThemeDataComposer
{
    protected ?array $data = null;

    public function compose(View $view): void
    {
        $view->with($this->data());
    }

    protected function data(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        // Composer ini juga aktif untuk view errors::* — saat database
        // bermasalah, halaman error tetap harus bisa dirender. Fallback
        // kosong membuat layout tampil tanpa menu dinamis.
        try {
            $settings = rescue(fn () => app(GeneralSettings::class), null, false);

            $menus = Menu::query()
                ->whereIn('location', ['primary', 'footer'])
                ->with(['rootItems.post', 'rootItems.children.post'])
                ->get()
                ->keyBy('location');

            return $this->data = [
                'settings' => $settings,
                'primaryMenu' => $menus->get('primary'),
                'footerMenu' => $menus->get('footer'),
            ];
        } catch (\Throwable) {
            return $this->data = [
                'settings' => null,
                'primaryMenu' => null,
                'footerMenu' => null,
            ];
        }
    }
}
