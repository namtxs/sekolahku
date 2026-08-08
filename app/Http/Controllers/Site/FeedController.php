<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Settings\GeneralSettings;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function __invoke(GeneralSettings $settings): Response
    {
        $posts = Post::posts()
            ->published()
            ->latest('published_at')
            ->take(20)
            ->get();

        return response()
            ->view('feeds.rss', compact('posts', 'settings'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
