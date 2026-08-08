<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Album;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = Album::query()
            ->where('is_published', true)
            ->with('media')
            ->latest()
            ->paginate(12);

        return theme_view('gallery.index', compact('albums'));
    }

    public function show(string $slug)
    {
        $album = Album::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->with('media')
            ->firstOrFail();

        return theme_view('gallery.show', compact('album'));
    }
}
