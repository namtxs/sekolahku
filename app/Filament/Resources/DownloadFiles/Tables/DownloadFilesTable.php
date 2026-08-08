<?php

namespace App\Filament\Resources\DownloadFiles\Tables;

use App\Models\DownloadFile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DownloadFilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('media'))
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('file_name')
                    ->label('Berkas')
                    ->state(fn (DownloadFile $record): ?string => $record->getFirstMedia('file')?->file_name)
                    ->description(fn (DownloadFile $record): ?string => $record->getFirstMedia('file')?->humanReadableSize)
                    ->limit(40),
                TextColumn::make('download_count')
                    ->label('Diunduh')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_published')
                    ->label('Terbit'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedArrowDownTray)
            ->emptyStateHeading('Belum ada berkas unduhan')
            ->emptyStateDescription('Unggah dokumen resmi sekolah agar dapat diunduh pengunjung.')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn (): array => DownloadFile::query()
                        ->whereNotNull('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category', 'category')
                        ->all()),
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
