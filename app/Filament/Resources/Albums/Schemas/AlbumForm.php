<?php

namespace App\Filament\Resources\Albums\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AlbumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Album')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Kosongkan untuk dibuat otomatis dari nama.'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Dipublikasikan')
                    ->default(true)
                    ->inline(false),
                SpatieMediaLibraryFileUpload::make('photos')
                    ->label('Foto')
                    ->collection('photos')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->maxSize(5120)
                    ->panelLayout('grid')
                    ->columnSpanFull(),
            ]);
    }
}
