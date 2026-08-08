<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['title', 'type', 'area', 'content', 'settings', 'is_active', 'sort_order'])]
class Widget extends Model
{
    /** Kunci cache data widget publik per area (di-flush saat widget berubah). */
    public const CACHE_KEYS = [
        'sidebar' => 'theme.widgets.sidebar',
        'page_sidebar' => 'theme.widgets.page_sidebar',
    ];

    /**
     * Tipe widget yang didukung tema: [type => label].
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'text' => 'Teks / HTML',
        'search' => 'Pencarian',
        'recent_posts' => 'Berita Terbaru',
        'popular_posts' => 'Berita Populer',
        'categories' => 'Kategori',
        'tags' => 'Tag',
        'archive' => 'Arsip Bulanan',
        'links' => 'Daftar Tautan',
        'social' => 'Media Sosial',
        'contact_info' => 'Info Kontak',
    ];

    protected static function booted(): void
    {
        $flush = function (): void {
            foreach (self::CACHE_KEYS as $key) {
                Cache::forget($key);
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Label tipe widget dalam Bahasa Indonesia.
     */
    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
