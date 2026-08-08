<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), fn () => $this->buildSitemap()->render());

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    protected function buildSitemap(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0))
            ->add(Url::create(route('posts.index'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9))
            ->add(Url::create(route('gallery.index'))->setPriority(0.6))
            ->add(Url::create(route('downloads.index'))->setPriority(0.6))
            ->add(Url::create(route('contact.show'))->setPriority(0.5));

        // Berita terbit.
        Post::posts()->published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at'])
            ->each(fn (Post $post) => $sitemap->add(
                Url::create(route('posts.show', $post->slug))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.8)
            ));

        // Halaman statis terbit.
        Post::pages()->published()
            ->get(['slug', 'updated_at'])
            ->each(fn (Post $page) => $sitemap->add(
                Url::create(route('pages.show', $page->slug))
                    ->setLastModificationDate($page->updated_at)
                    ->setPriority(0.7)
            ));

        $publishedPosts = fn ($query) => $query->posts()->published();

        // Kategori & tag yang memiliki berita terbit.
        Category::whereHas('posts', $publishedPosts)
            ->get(['slug'])
            ->each(fn (Category $category) => $sitemap->add(
                Url::create(route('categories.show', $category->slug))->setPriority(0.5)
            ));

        Tag::whereHas('posts', $publishedPosts)
            ->get(['slug'])
            ->each(fn (Tag $tag) => $sitemap->add(
                Url::create(route('tags.show', $tag->slug))->setPriority(0.4)
            ));

        // Album galeri terbit.
        Album::where('is_published', true)
            ->get(['slug', 'updated_at'])
            ->each(fn (Album $album) => $sitemap->add(
                Url::create(route('gallery.show', $album->slug))
                    ->setLastModificationDate($album->updated_at)
                    ->setPriority(0.6)
            ));

        return $sitemap;
    }
}
