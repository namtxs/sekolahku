<?php

namespace App\Filament\Resources\AdmissionPaths\Tables;

use App\Models\AdmissionPath;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdmissionPathsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('opens_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedArrowsPointingIn)
            ->emptyStateHeading('Belum ada jalur PPDB')
            ->emptyStateDescription('Buat jalur pendaftaran (mis. Zonasi, Prestasi, Afirmasi) beserta kuota dan periodenya.')
            ->columns([
                TextColumn::make('name')
                    ->label('Jalur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (AdmissionPath $record): string => $record->academicYear->name),
                TextColumn::make('quota')
                    ->label('Kuota')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('terpakai')
                    ->label('Terpakai')
                    ->state(fn (AdmissionPath $record): string => $record->usedQuota().' / sisa '.$record->remainingQuota())
                    ->tooltip('Terpakai = pendaftar terverifikasi + diterima'),
                TextColumn::make('periode')
                    ->label('Periode')
                    ->state(fn (AdmissionPath $record): string => $record->opens_at->format('d M Y H:i').' – '.$record->closes_at->format('d M Y H:i')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (AdmissionPath $record): string => match (true) {
                        ! $record->is_active => 'Nonaktif',
                        $record->isOpen() => 'Dibuka',
                        $record->isUpcoming() => 'Segera',
                        default => 'Ditutup',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Dibuka' => 'success',
                        'Segera' => 'info',
                        'Ditutup' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Pelajaran')
                    ->relationship('academicYear', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
