<?php

namespace App\View\Composers;

use App\Models\Menu;
use App\Models\Tag;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
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
        // kosong membuat layout tampil tanpa menu/tag dinamis.
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
                'footerTags' => $this->footerTags(),
            ];
        } catch (\Throwable) {
            return $this->data = [
                'settings' => null,
                'primaryMenu' => null,
                'footerMenu' => null,
                'footerTags' => [],
            ];
        }
    }

    /**
     * Tag populer untuk kolom "Tags" di footer. Disimpan sebagai array
     * polos agar aman di-cache (cache.serializable_classes = false).
     *
     * @return array<int, array{name: string, url: string}>
     */
    protected function footerTags(): array
    {
        return Cache::remember('theme.footer.tags', now()->addMinutes(10), function (): array {
            return Tag::query()
                ->withCount(['posts' => fn ($query) => $query->posts()->published()])
                ->orderByDesc('posts_count')
                ->take(8)
                ->get()
                ->filter(fn (Tag $tag): bool => $tag->posts_count > 0)
                ->map(fn (Tag $tag): array => [
                    'name' => $tag->name,
                    'url' => route('tags.show', $tag->slug),
                ])
                ->values()
                ->all();
        });
    }
}
