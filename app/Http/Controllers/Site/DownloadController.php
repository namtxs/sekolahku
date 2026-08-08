<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\DownloadFile;

class DownloadController extends Controller
{
    public function index()
    {
        $files = DownloadFile::query()
            ->where('is_published', true)
            ->with('media')
            ->latest()
            ->get()
            ->groupBy(fn (DownloadFile $file) => $file->category ?: 'Lainnya');

        return theme_view('downloads.index', compact('files'));
    }

    public function download(string $slug)
    {
        $file = DownloadFile::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $media = $file->getFirstMedia('file');

        abort_if($media === null, 404, 'Berkas tidak ditemukan.');

        $file->increment('download_count');

        return $media;
    }
}
