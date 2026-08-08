<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PageController extends Controller
{
    public function __invoke(string $slug)
    {
        $page = Post::pages()
            ->published()
            ->where('slug', $slug)
            ->with('media')
            ->firstOrFail();

        // Kartu navigasi "Halaman Lainnya" di bawah artikel.
        $otherPages = Post::pages()
            ->published()
            ->where('id', '!=', $page->id)
            ->orderBy('title')
            ->take(6)
            ->get(['id', 'title', 'slug', 'excerpt']);

        // Komentar hanya dimuat bila fitur diaktifkan untuk halaman ini.
        $comments = $page->allow_comments
            ? $page->comments()
                ->approved()
                ->whereNull('parent_id')
                ->with(['replies' => fn ($q) => $q->where('status', 'approved')->oldest()])
                ->oldest()
                ->get()
            : collect();

        return theme_view('page', compact('page', 'otherPages', 'comments'));
    }
}
