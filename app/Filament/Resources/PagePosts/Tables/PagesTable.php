<?php

namespace App\Filament\Resources\PagePosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured')
                    ->label('Gambar')
                    ->collection('featured')
                    ->conversion('thumb')
                    ->imageSize(44),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draf',
                        'published' => 'Terbit',
                        'scheduled' => 'Terjadwal',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('Waktu Terbit')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('views')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedDocumentText)
            ->emptyStateHeading('Belum ada halaman')
            ->emptyStateDescription('Buat halaman statis seperti Profil Sekolah, Visi & Misi, atau Sejarah.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draf',
                        'published' => 'Terbit',
                        'scheduled' => 'Terjadwal',
                    ]),
                TrashedFilter::make()->label('Terhapus'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make()->label('Pulihkan'),
                ForceDeleteAction::make()->label('Hapus Permanen'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make()->label('Pulihkan'),
                    ForceDeleteBulkAction::make()->label('Hapus Permanen'),
                ]),
            ]);
    }
}
