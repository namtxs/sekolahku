<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['menu_id', 'parent_id', 'title', 'url', 'post_id', 'target', 'sort_order'])]
class MenuItem extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * URL final item menu: pakai kolom url bila diisi,
     * atau tautan ke post/page terkait.
     */
    public function resolvedUrl(): string
    {
        if (filled($this->url)) {
            return $this->url;
        }

        if ($this->post) {
            return $this->post->type === 'post'
                ? route('posts.show', $this->post->slug)
                : url('/'.$this->post->slug);
        }

        return '#';
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
