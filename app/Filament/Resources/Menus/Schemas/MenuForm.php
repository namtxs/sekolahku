<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Menu')
                    ->required()
                    ->maxLength(255),
                TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Contoh: header, footer. Setiap lokasi hanya boleh dipakai satu menu.'),
            ]);
    }
}
