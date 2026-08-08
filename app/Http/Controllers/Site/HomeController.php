<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Banner;
use App\Models\Post;
use App\Settings\GeneralSettings;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __invoke()
    {
        $banners = Banner::active()
            ->orderBy('sort_order')
            ->with('media')
            ->get();

        $latestPosts = Post::posts()
            ->published()
            ->with(['category', 'media', 'user'])
            ->latest('published_at')
            ->take(5)
            ->get();

        $announcements = Post::posts()
            ->published()
            ->whereHas('category', fn ($query) => $query->where('slug', 'pengumuman'))
            ->latest('published_at')
            ->take(6)
            ->get();

        $albums = Album::query()
            ->where('is_published', true)
            ->with('media')
            ->latest()
            ->take(4)
            ->get();

        $headmasterQuote = $this->headmasterQuote(app(GeneralSettings::class)->headmaster_greeting);

        return theme_view('home', compact('banners', 'latestPosts', 'announcements', 'albums', 'headmasterQuote'));
    }

    /**
     * Ambil kutipan pendek yang kuat dari teks sambutan: lewati kalimat salam
     * pembuka, lalu potong pada batas kalimat/kata yang wajar.
     */
    private function headmasterQuote(?string $greeting): string
    {
        // Sisipkan spasi pada batas paragraf/baris agar kalimat tidak menempel setelah strip_tags.
        $text = trim(strip_tags(preg_replace('~</(?:p|div|h[1-6]|li)>|<br\s*/?>~i', ' ', $greeting ?? '')));
        $text = preg_replace('/\s+/u', ' ', $text);

        if ($text === '') {
            return '';
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];

        while (count($sentences) > 1 && preg_match('/^(assalamu|wassalamu|salam |salam,|om swasti|shalom|namo buddhaya|puji syukur)/iu', trim($sentences[0]))) {
            array_shift($sentences);
        }

        $quote = '';

        foreach ($sentences as $sentence) {
            $quote = trim($quote.' '.$sentence);

            if (Str::wordCount($quote) >= 18) {
                break;
            }
        }

        return Str::words(rtrim($quote, '.'), 32);
    }
}
