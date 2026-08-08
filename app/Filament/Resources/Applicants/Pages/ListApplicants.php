<?php

namespace App\Filament\Resources\Applicants\Pages;

use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Ekspor CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->exportCsv()),
        ];
    }

    /**
     * Ekspor pendaftar sesuai filter/pencarian tabel yang sedang aktif,
     * sebagai streaming CSV tanpa paket tambahan.
     */
    protected function exportCsv(): StreamedResponse
    {
        $query = $this->getFilteredTableQuery()->with('path.academicYear');

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No. Pendaftaran', 'Nama', 'NISN', 'Jenis Kelamin', 'Tempat Lahir',
                'Tanggal Lahir', 'Agama', 'Alamat', 'No. HP', 'Email', 'Asal Sekolah',
                'Nama Orang Tua', 'No. HP Orang Tua', 'Rata-rata Nilai', 'Jalur',
                'Tahun Pelajaran', 'Status', 'Catatan', 'Tanggal Daftar',
            ]);

            $query->latest()->chunk(200, function ($applicants) use ($handle): void {
                foreach ($applicants as $applicant) {
                    fputcsv($handle, [
                        $applicant->registration_number,
                        $applicant->name,
                        $applicant->nisn,
                        $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                        $applicant->birth_place,
                        $applicant->birth_date->format('Y-m-d'),
                        $applicant->religion,
                        $applicant->address,
                        $applicant->phone,
                        $applicant->email,
                        $applicant->school_origin,
                        $applicant->parent_name,
                        $applicant->parent_phone,
                        $applicant->avg_score,
                        $applicant->path->name,
                        $applicant->path->academicYear->name,
                        Applicant::STATUS_LABELS[$applicant->status] ?? $applicant->status,
                        $applicant->verification_note,
                        $applicant->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 'pendaftar-ppdb-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
