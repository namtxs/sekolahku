<?php

namespace App\Filament\Resources\Widgets\Tables;

use App\Enums\WidgetArea;
use App\Models\Widget;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class WidgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->emptyStateIcon(Heroicon::OutlinedPuzzlePiece)
            ->emptyStateHeading('Belum ada widget')
            ->emptyStateDescription('Widget tampil pada sidebar halaman berita dan halaman statis. Buat widget pertama Anda.')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Widget::TYPES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'text', 'links' => 'info',
                        'search' => 'gray',
                        'recent_posts', 'popular_posts' => 'success',
                        'categories', 'tags', 'archive' => 'warning',
                        default => 'primary',
                    }),
                TextColumn::make('area')
                    ->label('Area')
                    ->formatStateUsing(fn (string $state): string => WidgetArea::tryFrom($state)?->label() ?? $state),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->label('Area')
                    ->options(WidgetArea::options()),
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(Widget::TYPES),
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
