<?php

namespace App\Filament\Resources\Banners\Tables;

use App\Models\Banner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->emptyStateIcon(Heroicon::OutlinedRectangleGroup)
            ->emptyStateHeading('Belum ada banner')
            ->emptyStateDescription('Banner tampil sebagai slider di halaman beranda. Ukuran ideal 1920x700 piksel.')
            ->columns([
                SpatieMediaLibraryImageColumn::make('banner')
                    ->label('Gambar')
                    ->collection('banner')
                    ->conversion('thumb')
                    ->imageSize(44),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('periode')
                    ->label('Periode Tayang')
                    ->state(function (Banner $record): string {
                        if (! $record->starts_at && ! $record->ends_at) {
                            return 'Selalu tayang';
                        }

                        $start = $record->starts_at?->format('d M Y H:i') ?? '…';
                        $end = $record->ends_at?->format('d M Y H:i') ?? '…';

                        return "{$start} – {$end}";
                    }),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
