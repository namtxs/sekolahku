<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\AlbumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['name', 'slug', 'description', 'is_published'])]
class Album extends Model implements HasMedia
{
    /** @use HasFactory<AlbumFactory> */
    use HasFactory, HasSlug, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')->useDisk('public');
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
            'is_published' => 'boolean',
        ];
    }
}
