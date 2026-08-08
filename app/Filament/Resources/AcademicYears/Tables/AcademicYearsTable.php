<?php

namespace App\Filament\Resources\AcademicYears\Tables;

use App\Models\AcademicYear;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedCalendarDays)
            ->emptyStateHeading('Belum ada tahun pelajaran')
            ->emptyStateDescription('Buat tahun pelajaran (mis. 2026/2027) lalu jadikan aktif untuk membuka PPDB.')
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('paths_count')
                    ->label('Jumlah Jalur')
                    ->counts('paths'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('makeActive')
                    ->label('Jadikan Aktif')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (AcademicYear $record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading(fn (AcademicYear $record): string => "Aktifkan tahun pelajaran {$record->name}?")
                    ->modalDescription('Tahun pelajaran lain akan otomatis dinonaktifkan.')
                    ->action(function (AcademicYear $record): void {
                        $record->makeActive();

                        Notification::make()
                            ->title("Tahun pelajaran {$record->name} sekarang aktif.")
                            ->success()
                            ->send();
                    }),
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
