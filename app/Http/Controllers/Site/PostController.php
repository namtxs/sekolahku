<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostController extends Controller
{
    public function index()
    {
        return theme_view('posts.index', [
            'title' => 'Berita',
            'posts' => $this->basePostQuery()->paginate(9),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::posts()
            ->published()
            ->where('slug', $slug)
            ->with(['user', 'category', 'tags', 'media'])
            ->firstOrFail();

        // Hitung views sekali per sesi pengunjung.
        $sessionKey = "viewed_post_{$post->id}";
        if (! session()->has($sessionKey)) {
            $post->increment('views');
            session()->put($sessionKey, true);
        }

        $related = Post::posts()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->with(['category', 'media'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $comments = $post->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with(['replies' => fn ($q) => $q->where('status', 'approved')->oldest()])
            ->oldest()
            ->get();

        return theme_view('posts.show', compact('post', 'related', 'comments'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return theme_view('posts.index', [
            'title' => 'Kategori: '.$category->name,
            'description' => $category->description,
            'posts' => $this->basePostQuery()
                ->where('category_id', $category->id)
                ->paginate(9),
        ]);
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        return theme_view('posts.index', [
            'title' => 'Tag: '.$tag->name,
            'posts' => $this->basePostQuery()
                ->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))
                ->paginate(9),
        ]);
    }

    public function archive(int $year, int $month)
    {
        abort_unless($month >= 1 && $month <= 12, 404);

        $label = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

        return theme_view('posts.index', [
            'title' => 'Arsip: '.$label,
            'posts' => $this->basePostQuery()
                ->whereYear('published_at', $year)
                ->whereMonth('published_at', $month)
                ->paginate(9),
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        $posts = $this->basePostQuery()
            ->when($query !== '', function ($q) use ($query) {
                $q->where(fn ($sub) => $sub
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('body', 'like', "%{$query}%"));
            })
            ->paginate(9)
            ->withQueryString();

        return theme_view('posts.index', [
            'title' => $query !== '' ? 'Hasil Pencarian: "'.$query.'"' : 'Pencarian',
            'posts' => $posts,
            'searchQuery' => $query,
        ]);
    }

    protected function basePostQuery()
    {
        return Post::posts()
            ->published()
            ->with(['category', 'media'])
            ->latest('published_at');
    }
}
