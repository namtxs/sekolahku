<?php

use App\Http\Controllers\Site\CommentController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\DownloadController;
use App\Http\Controllers\Site\GalleryController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\FeedController;
use App\Http\Controllers\Site\PostController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// SEO
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/feed', FeedController::class)->name('feed');

// Berita
Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('posts.show');
// Komentar: berlaku untuk berita maupun halaman statis ber-allow_comments.
Route::post('/komentar/{slug}', [CommentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('comments.store');

// Filter berita
Route::get('/kategori/{slug}', [PostController::class, 'category'])->name('categories.show');
Route::get('/tag/{slug}', [PostController::class, 'tag'])->name('tags.show');
Route::get('/arsip/{year}/{month}', [PostController::class, 'archive'])
    ->whereNumber('year')
    ->whereNumber('month')
    ->name('posts.archive');
Route::get('/cari', [PostController::class, 'search'])->name('posts.search');

// Galeri
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/galeri/{slug}', [GalleryController::class, 'show'])->name('gallery.show');

// Unduhan
Route::get('/unduhan', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/unduhan/{slug}/download', [DownloadController::class, 'download'])->name('downloads.download');

// Kontak
Route::get('/kontak', [ContactController::class, 'show'])->name('contact.show');
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Halaman statis (paling bawah agar tidak menimpa route lain).
Route::get('/{slug}', PageController::class)
    ->where('slug', '^(?!admin|livewire)[a-zA-Z0-9\-_]+$')
    ->name('pages.show');
