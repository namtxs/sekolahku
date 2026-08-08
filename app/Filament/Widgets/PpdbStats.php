<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\AcademicYear;
use App\Models\Applicant;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Ringkasan PPDB tahun pelajaran aktif: total pendaftar per status
 * dan sisa kuota tiap jalur.
 */
class PpdbStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'PPDB Tahun Aktif';

    public static function canView(): bool
    {
        return (auth()->user()?->can('ppdb.view_any') ?? false)
            && AcademicYear::active() !== null;
    }

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $year = AcademicYear::active();

        if ($year === null) {
            return [];
        }

        $this->heading = 'PPDB '.$year->name;

        $byStatus = Applicant::query()
            ->whereHas('path', fn ($query) => $query->where('academic_year_id', $year->id))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            Stat::make('Total Pendaftar', $byStatus->sum())
                ->description('Seluruh jalur tahun '.$year->name)
                ->icon(Heroicon::OutlinedUserPlus)
                ->url(ApplicantResource::getUrl())
                ->color('primary')
                ->extraAttributes(['class' => 'sk-stat sk-stat-blue']),
            Stat::make('Menunggu Verifikasi', $byStatus[Applicant::STATUS_PENDING] ?? 0)
                ->description('Perlu pemeriksaan berkas')
                ->icon(Heroicon::OutlinedClock)
                ->color('warning')
                ->extraAttributes(['class' => 'sk-stat sk-stat-amber']),
            Stat::make('Terverifikasi', $byStatus[Applicant::STATUS_VERIFIED] ?? 0)
                ->description('Menunggu keputusan')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->color('info')
                ->extraAttributes(['class' => 'sk-stat sk-stat-indigo']),
            Stat::make('Diterima', $byStatus[Applicant::STATUS_ACCEPTED] ?? 0)
                ->description('Ditolak: '.($byStatus[Applicant::STATUS_REJECTED] ?? 0))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->extraAttributes(['class' => 'sk-stat sk-stat-green']),
        ];

        foreach ($year->paths()->orderBy('name')->get() as $path) {
            $stats[] = Stat::make('Sisa Kuota '.$path->name, $path->remainingQuota())
                ->description('Kuota '.$path->quota.' — terpakai '.$path->usedQuota())
                ->icon(Heroicon::OutlinedTicket)
                ->color($path->remainingQuota() > 0 ? 'success' : 'danger')
                ->extraAttributes(['class' => 'sk-stat '.($path->remainingQuota() > 0 ? 'sk-stat-green' : 'sk-stat-red')]);
        }

        return $stats;
    }
}
