<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Mengisi kolom `slug` secara otomatis dari kolom sumber saat model dibuat,
 * dengan suffix angka (-2, -3, ...) bila slug sudah dipakai.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            if (blank($model->slug)) {
                $model->slug = $model->generateUniqueSlug(
                    (string) $model->getAttribute($model->slugSourceColumn()),
                );
            }
        });
    }

    /**
     * Kolom sumber pembentukan slug. Override di model bila bukan `name`.
     */
    public function slugSourceColumn(): string
    {
        return 'name';
    }

    protected function generateUniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while ($this->newSlugQuery()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function newSlugQuery(): Builder
    {
        $query = static::query();

        // Slug harus unik juga terhadap record yang di-soft-delete.
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query->withTrashed();
        }

        return $query;
    }
}
