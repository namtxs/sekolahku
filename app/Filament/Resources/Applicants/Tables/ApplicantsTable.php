<?php

namespace App\Filament\Resources\Applicants\Tables;

use App\Filament\Resources\Applicants\ApplicantActions;
use App\Models\Applicant;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('path.academicYear'))
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedUserPlus)
            ->emptyStateHeading('Belum ada pendaftar')
            ->emptyStateDescription('Pendaftar dari formulir PPDB di situs publik akan muncul di sini.')
            ->columns([
                TextColumn::make('registration_number')
                    ->label('No. Pendaftaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Applicant $record): string => 'NISN '.$record->nisn),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('path.name')
                    ->label('Jalur'),
                TextColumn::make('school_origin')
                    ->label('Asal Sekolah')
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('avg_score')
                    ->label('Nilai')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Applicant::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Applicant::STATUS_PENDING => 'gray',
                        Applicant::STATUS_VERIFIED => 'info',
                        Applicant::STATUS_ACCEPTED => 'success',
                        Applicant::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('admission_path_id')
                    ->label('Jalur')
                    ->relationship('path', 'name'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Applicant::STATUS_LABELS),
            ])
            ->recordActions([
                ViewAction::make(),
                ApplicantActions::verify(),
                ApplicantActions::accept(),
                ApplicantActions::reject(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('verify')
                        ->label('Verifikasi Terpilih')
                        ->icon(Heroicon::OutlinedShieldCheck)
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalDescription('Hanya pendaftar berstatus "Menunggu Verifikasi" yang akan diproses.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $verified = 0;

                            foreach ($records as $record) {
                                if ($record->status === Applicant::STATUS_PENDING) {
                                    $record->verify();
                                    $verified++;
                                }
                            }

                            Notification::make()
                                ->title("{$verified} pendaftar terverifikasi.")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
