<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\DownloadFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['title', 'slug', 'description', 'category', 'download_count', 'is_published', 'user_id'])]
class DownloadFile extends Model implements HasMedia
{
    /** @use HasFactory<DownloadFileFactory> */
    use HasFactory, HasSlug, InteractsWithMedia;

    public function slugSourceColumn(): string
    {
        return 'title';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        // Berkas unduhan (PDF, dokumen, arsip, dsb.) — tanpa konversi gambar.
        $this->addMediaCollection('file')->useDisk('public')->singleFile();
    }

    protected function casts(): array
    {
        return [
            'download_count' => 'integer',
            'is_published' => 'boolean',
        ];
    }
}
