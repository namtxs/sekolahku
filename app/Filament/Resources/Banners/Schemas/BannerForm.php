<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('Tautan (URL)')
                    ->maxLength(255)
                    ->regex('~^(https?://|/)~')
                    ->validationMessages([
                        'regex' => 'Tautan harus berupa URL lengkap (diawali http:// atau https://) atau path relatif yang diawali garis miring (/).',
                    ])
                    ->helperText('Boleh URL lengkap (https://...) atau path relatif (/berita).'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('starts_at')
                    ->label('Mulai Tayang')
                    ->seconds(false),
                DateTimePicker::make('ends_at')
                    ->label('Selesai Tayang')
                    ->seconds(false)
                    ->after('starts_at'),
                SpatieMediaLibraryFileUpload::make('banner')
                    ->label('Gambar Banner')
                    ->collection('banner')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->maxSize(5120)
                    ->helperText('Maksimal 5 MB. Ukuran ideal 1920×700 piksel agar tajam di slider beranda.')
                    ->columnSpanFull(),
            ]);
    }
}
