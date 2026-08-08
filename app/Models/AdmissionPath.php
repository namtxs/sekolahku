<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\AdmissionPathFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'academic_year_id',
    'name',
    'slug',
    'description',
    'quota',
    'requirements',
    'opens_at',
    'closes_at',
    'is_active',
])]
class AdmissionPath extends Model
{
    /** @use HasFactory<AdmissionPathFactory> */
    use HasFactory, HasSlug, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * Jalur yang sedang menerima pendaftaran: aktif dan dalam periode buka.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('opens_at', '<=', now())
            ->where('closes_at', '>=', now());
    }

    public function isOpen(): bool
    {
        return $this->is_active
            && $this->opens_at !== null && $this->opens_at->isPast()
            && $this->closes_at !== null && $this->closes_at->isFuture();
    }

    /**
     * Jalur aktif yang periodenya belum dimulai ("Segera").
     */
    public function isUpcoming(): bool
    {
        return $this->is_active && $this->opens_at !== null && $this->opens_at->isFuture();
    }

    /**
     * Kuota terpakai = pendaftar terverifikasi + diterima.
     *
     * Keputusan desain: pendaftar berstatus verified dihitung "memegang kursi"
     * karena berkasnya sudah sah dan menunggu keputusan, sehingga sisa kuota
     * yang ditampilkan ke publik bersifat konservatif (tidak menjanjikan kursi
     * yang kemungkinan besar sudah terisi). Pendaftar pending/rejected tidak
     * dihitung.
     */
    public function usedQuota(): int
    {
        return $this->applicants()
            ->whereIn('status', [Applicant::STATUS_VERIFIED, Applicant::STATUS_ACCEPTED])
            ->count();
    }

    public function remainingQuota(): int
    {
        return max(0, $this->quota - $this->usedQuota());
    }

    /**
     * Jumlah pendaftar yang sudah diterima (dipakai guard kuota saat menerima).
     */
    public function acceptedCount(): int
    {
        return $this->applicants()
            ->where('status', Applicant::STATUS_ACCEPTED)
            ->count();
    }

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
