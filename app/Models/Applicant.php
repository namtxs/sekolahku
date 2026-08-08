<?php

namespace App\Models;

use App\Mail\ApplicantDecisionMail;
use Database\Factories\ApplicantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Pendaftar PPDB. Alur status: pending → verified → accepted/rejected.
 */
#[Fillable([
    'registration_number',
    'admission_path_id',
    'name',
    'nisn',
    'gender',
    'birth_place',
    'birth_date',
    'religion',
    'address',
    'phone',
    'email',
    'school_origin',
    'parent_name',
    'parent_phone',
    'avg_score',
    'status',
    'verification_note',
    'verified_at',
    'decided_at',
])]
class Applicant extends Model implements HasMedia
{
    /** @use HasFactory<ApplicantFactory> */
    use HasFactory, InteractsWithMedia, LogsActivity;

    /**
     * Jejak audit PPDB fokus pada perubahan status verifikasi/keputusan,
     * bukan seluruh biodata pendaftar.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'verification_note'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu Verifikasi',
        self::STATUS_VERIFIED => 'Terverifikasi',
        self::STATUS_ACCEPTED => 'Diterima',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    protected static function booted(): void
    {
        static::creating(function (Applicant $applicant) {
            if (blank($applicant->registration_number)) {
                $applicant->registration_number = static::generateRegistrationNumber(
                    $applicant->path()->firstOrFail(),
                );
            }
        });

        // Email hasil keputusan ke pendaftar (bila punya email), apa pun
        // jalur pemanggilannya (aksi panel, tinker, dsb.) — via antrean
        // agar gagal kirim tidak mematikan request.
        static::updated(function (Applicant $applicant) {
            if (! $applicant->wasChanged('status')) {
                return;
            }

            if (! in_array($applicant->status, [self::STATUS_ACCEPTED, self::STATUS_REJECTED], true)) {
                return;
            }

            if (blank($applicant->email)) {
                return;
            }

            Mail::to($applicant->email)->queue(new ApplicantDecisionMail($applicant));
        });
    }

    /**
     * Nomor pendaftaran "PPDB-{tahun awal ajaran}-{urut 4 digit}",
     * berurutan per tahun ajaran.
     */
    public static function generateRegistrationNumber(AdmissionPath $path): string
    {
        $year = $path->academicYear->startYear();
        $prefix = "PPDB-{$year}-";

        $last = static::query()
            ->where('registration_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('registration_number')
            ->value('registration_number');

        $next = $last ? ((int) str($last)->afterLast('-')->toString()) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(AdmissionPath::class, 'admission_path_id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Verifikasi berkas: pending → verified.
     */
    public function verify(?string $note = null): void
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verification_note' => $note ?: $this->verification_note,
            'verified_at' => now(),
        ]);
    }

    /**
     * Terima pendaftar (verified → accepted) dengan guard kuota:
     * gagal (return false) bila jumlah diterima sudah mencapai kuota jalur.
     */
    public function accept(): bool
    {
        if ($this->status !== self::STATUS_VERIFIED) {
            return false;
        }

        if ($this->path->acceptedCount() >= $this->path->quota) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'decided_at' => now(),
        ]);

        return true;
    }

    /**
     * Tolak pendaftar (verified → rejected).
     */
    public function reject(?string $note = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verification_note' => $note ?: $this->verification_note,
            'decided_at' => now(),
        ]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('documents')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 400)
            ->performOnCollections('photo')
            ->nonQueued();
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'avg_score' => 'decimal:2',
            'verified_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }
}
