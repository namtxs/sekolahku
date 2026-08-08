<?php

namespace App\Filament\Resources\Posts\Tables;

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

class PostsTable
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
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Penulis')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedNewspaper)
            ->emptyStateHeading('Belum ada berita')
            ->emptyStateDescription('Mulai bagikan kabar sekolah dengan menulis berita pertama Anda.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draf',
                        'published' => 'Terbit',
                        'scheduled' => 'Terjadwal',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
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
