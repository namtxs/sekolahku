<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Models\MenuItem;
use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Menu';

    protected static ?string $modelLabel = 'Item Menu';

    protected static ?string $pluralModelLabel = 'Item Menu';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                Select::make('link_type')
                    ->label('Tipe Tautan')
                    ->options([
                        'url' => 'URL Manual',
                        'post' => 'Halaman / Berita',
                    ])
                    ->required()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Select $component, ?MenuItem $record): void {
                        $component->state($record?->post_id ? 'post' : 'url');
                    })
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        if ($state === 'url') {
                            $set('post_id', null);
                        } else {
                            $set('url', null);
                        }
                    }),
                TextInput::make('url')
                    ->label('URL')
                    ->maxLength(255)
                    ->regex('~^(https?://|/|#)~')
                    ->validationMessages([
                        'regex' => 'Tautan harus berupa URL lengkap (diawali http:// atau https://), path relatif yang diawali garis miring (/), atau anchor (#).',
                    ])
                    ->helperText('Boleh URL lengkap (https://...) atau path relatif (/berita).')
                    ->visible(fn (Get $get): bool => $get('link_type') === 'url')
                    ->required(fn (Get $get): bool => $get('link_type') === 'url'),
                Select::make('post_id')
                    ->label('Halaman / Berita')
                    ->options(fn (): array => Post::query()
                        ->orderBy('type')
                        ->orderBy('title')
                        ->get()
                        ->mapWithKeys(fn (Post $post): array => [
                            $post->id => ($post->type === 'page' ? '[Halaman] ' : '[Berita] ').$post->title,
                        ])
                        ->all())
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'post')
                    ->required(fn (Get $get): bool => $get('link_type') === 'post'),
                Select::make('target')
                    ->label('Target')
                    ->options([
                        '_self' => 'Tab yang sama',
                        '_blank' => 'Tab baru',
                    ])
                    ->default('_self'),
                Select::make('parent_id')
                    ->label('Induk')
                    ->options(fn (?MenuItem $record): array => MenuItem::query()
                        ->where('menu_id', $this->getOwnerRecord()->getKey())
                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                        ->orderBy('sort_order')
                        ->pluck('title', 'id')
                        ->all())
                    ->placeholder('— Tanpa induk (level atas) —'),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->formatStateUsing(function (string $state, MenuItem $record): string {
                        $depth = 0;
                        $parent = $record->parent;

                        while ($parent !== null && $depth < 5) {
                            $depth++;
                            $parent = $parent->parent;
                        }

                        return str_repeat('— ', $depth).$state;
                    })
                    ->searchable(),
                TextColumn::make('tautan')
                    ->label('Tautan')
                    ->state(fn (MenuItem $record): string => $record->post_id
                        ? ($record->post?->title ?? '(tulisan terhapus)')
                        : ($record->url ?? '-'))
                    ->badge()
                    ->color(fn (MenuItem $record): string => $record->post_id ? 'info' : 'gray'),
                TextColumn::make('parent.title')
                    ->label('Induk')
                    ->placeholder('—'),
                TextColumn::make('target')
                    ->label('Target'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
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
