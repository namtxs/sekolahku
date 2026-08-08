<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Menyediakan widget sidebar aktif (beserta datanya) untuk
 * partial themes.*.partials.sidebar. Widget dikelola dari panel admin.
 *
 * Yang disimpan ke cache hanya array polos (bukan model Eloquent) karena
 * konfigurasi cache.serializable_classes = false melarang unserialize objek.
 * Cache di-flush otomatis saat widget disimpan/dihapus (event model Widget).
 */
class SidebarComposer
{
    /** @var array<string, array> Cache per-request per area. */
    protected array $widgets = [];

    public function compose(View $view): void
    {
        // Partial page-sidebar merender area page_sidebar (halaman statis),
        // partial sidebar merender area sidebar (berita dan beranda).
        $area = str_ends_with($view->name(), 'page-sidebar') ? 'page_sidebar' : 'sidebar';

        $view->with('sidebarWidgets', $this->widgets($area));
    }

    /**
     * @return array<int, array{widget: object, data: mixed}>
     */
    protected function widgets(string $area): array
    {
        $this->widgets[$area] ??= Cache::remember(Widget::CACHE_KEYS[$area], now()->addMinutes(5), function () use ($area): array {
            return Widget::query()
                ->active()
                ->where('area', $area)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Widget $widget): array => [
                    'widget' => [
                        'id' => $widget->id,
                        'title' => $widget->title,
                        'type' => $widget->type,
                        'content' => $widget->content,
                        'settings' => $widget->settings ?? [],
                    ],
                    'data' => $this->dataFor($widget),
                ])
                ->all();
        });

        // Partial widget memakai sintaks objek ($widget->title, dst.).
        return array_map(fn (array $item): array => [
            'widget' => (object) $item['widget'],
            'data' => $item['data'],
        ], $this->widgets[$area]);
    }

    /**
     * Query data hanya untuk tipe widget yang membutuhkannya,
     * lalu petakan menjadi array polos siap render (aman di-cache).
     */
    protected function dataFor(Widget $widget): mixed
    {
        $limit = (int) ($widget->settings['limit'] ?? 0);

        return match ($widget->type) {
            'recent_posts' => Post::posts()
                ->published()
                ->with('media')
                ->latest('published_at')
                ->take($limit > 0 ? $limit : 5)
                ->get()
                ->map(fn (Post $post): array => $this->postItem($post))
                ->all(),

            'popular_posts' => Post::posts()
                ->published()
                ->with('media')
                ->orderByDesc('views')
                ->take($limit > 0 ? $limit : 5)
                ->get()
                ->map(fn (Post $post): array => $this->postItem($post))
                ->all(),

            'categories' => Category::query()
                ->withCount(['posts' => fn ($query) => $query->posts()->published()])
                ->orderBy('name')
                ->get()
                ->filter(fn (Category $category): bool => $category->posts_count > 0)
                ->when($limit > 0, fn ($categories) => $categories->take($limit))
                ->map(fn (Category $category): array => [
                    'name' => $category->name,
                    'url' => route('categories.show', $category->slug),
                    'count' => $category->posts_count,
                ])
                ->values()
                ->all(),

            'tags' => Tag::query()
                ->withCount(['posts' => fn ($query) => $query->posts()->published()])
                ->orderByDesc('posts_count')
                ->take($limit > 0 ? $limit : 20)
                ->get()
                ->filter(fn (Tag $tag): bool => $tag->posts_count > 0)
                ->map(fn (Tag $tag): array => [
                    'name' => $tag->name,
                    'url' => route('tags.show', $tag->slug),
                    'count' => $tag->posts_count,
                ])
                ->values()
                ->all(),

            'archive' => Post::posts()
                ->published()
                ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as total')
                ->groupBy('year', 'month')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->limit($limit > 0 ? $limit : 12)
                ->get()
                ->map(fn ($archive): array => [
                    'label' => tanggal_id(Carbon::createFromDate($archive->year, $archive->month, 1), 'F Y'),
                    'url' => route('posts.archive', ['year' => $archive->year, 'month' => $archive->month]),
                    'count' => $archive->total,
                ])
                ->all(),

            default => null,
        };
    }

    /**
     * @return array{title: string, url: string, thumb: ?string, date: string, views: int}
     */
    protected function postItem(Post $post): array
    {
        return [
            'title' => $post->title,
            'url' => route('posts.show', $post->slug),
            'thumb' => $post->getFirstMediaUrl('featured', 'thumb') ?: null,
            'date' => tanggal_id($post->published_at),
            'views' => (int) $post->views,
        ];
    }
}
