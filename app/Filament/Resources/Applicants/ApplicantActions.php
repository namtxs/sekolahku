<?php

namespace App\Filament\Resources\Applicants;

use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Aksi alur status pendaftar (pending → verified → accepted/rejected),
 * dipakai bersama oleh tabel dan halaman detail.
 */
class ApplicantActions
{
    public static function verify(): Action
    {
        return Action::make('verify')
            ->label('Verifikasi')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->color('info')
            ->visible(fn (Applicant $record): bool => $record->status === Applicant::STATUS_PENDING)
            ->schema([
                Textarea::make('note')
                    ->label('Catatan Verifikasi (opsional)')
                    ->rows(3),
            ])
            ->modalHeading(fn (Applicant $record): string => "Verifikasi berkas {$record->name}?")
            ->action(function (Applicant $record, array $data): void {
                $record->verify($data['note'] ?? null);

                Notification::make()
                    ->title("Pendaftar {$record->registration_number} terverifikasi.")
                    ->success()
                    ->send();
            });
    }

    public static function accept(): Action
    {
        return Action::make('accept')
            ->label('Terima')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->visible(fn (Applicant $record): bool => $record->status === Applicant::STATUS_VERIFIED)
            ->requiresConfirmation()
            ->modalHeading(fn (Applicant $record): string => "Terima {$record->name}?")
            ->modalDescription(fn (Applicant $record): string => "Jalur {$record->path->name}: sisa kuota penerimaan ".max(0, $record->path->quota - $record->path->acceptedCount()).' dari '.$record->path->quota.'.')
            ->action(function (Applicant $record): void {
                if (! $record->accept()) {
                    Notification::make()
                        ->title('Kuota jalur sudah penuh.')
                        ->body("Jumlah pendaftar diterima pada jalur {$record->path->name} sudah mencapai kuota ({$record->path->quota}). Tambah kuota jalur atau tolak pendaftar lain terlebih dahulu.")
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title("Pendaftar {$record->registration_number} diterima.")
                    ->success()
                    ->send();
            });
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Tolak')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->visible(fn (Applicant $record): bool => $record->status === Applicant::STATUS_VERIFIED)
            ->schema([
                Textarea::make('note')
                    ->label('Alasan Penolakan (opsional)')
                    ->rows(3),
            ])
            ->modalHeading(fn (Applicant $record): string => "Tolak {$record->name}?")
            ->action(function (Applicant $record, array $data): void {
                $record->reject($data['note'] ?? null);

                Notification::make()
                    ->title("Pendaftar {$record->registration_number} ditolak.")
                    ->success()
                    ->send();
            });
    }
}
