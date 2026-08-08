<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'type',
    'title',
    'slug',
    'excerpt',
    'body',
    'status',
    'published_at',
    'user_id',
    'category_id',
    'views',
    'allow_comments',
    'meta_title',
    'meta_description',
])]
class Post extends Model implements HasMedia
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasSlug, InteractsWithMedia, LogsActivity, SoftDeletes;

    /**
     * Kolom `views` dikecualikan agar increment penghitung kunjungan
     * tidak membanjiri log aktivitas.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['views'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function slugSourceColumn(): string
    {
        return 'title';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Konten terbit: status published dan waktu terbit sudah lewat.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePosts(Builder $query): Builder
    {
        return $query->where('type', 'post');
    }

    public function scopePages(Builder $query): Builder
    {
        return $query->where('type', 'page');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')->useDisk('public')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 400)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 800, 800)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 1600, 1600)
            ->nonQueued();
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views' => 'integer',
            'allow_comments' => 'boolean',
        ];
    }
}
