<?php

namespace App\Models;

use Database\Factories\AcademicYearFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['name', 'is_active'])]
class AcademicYear extends Model
{
    /** @use HasFactory<AcademicYearFactory> */
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted(): void
    {
        // Hanya satu tahun pelajaran yang boleh aktif: mengaktifkan satu
        // record otomatis menonaktifkan record lainnya.
        static::saved(function (AcademicYear $year) {
            if ($year->is_active) {
                static::query()
                    ->whereKeyNot($year->getKey())
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    public static function active(): ?self
    {
        return static::query()->where('is_active', true)->first();
    }

    public function paths(): HasMany
    {
        return $this->hasMany(AdmissionPath::class);
    }

    /**
     * Jadikan tahun ini aktif dan nonaktifkan tahun lainnya.
     */
    public function makeActive(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Tahun awal dari nama "2026/2027" → "2026" (dipakai nomor pendaftaran).
     */
    public function startYear(): string
    {
        return (string) str($this->name)->before('/')->trim();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
