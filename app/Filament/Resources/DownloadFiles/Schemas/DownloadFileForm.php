<?php

namespace App\Filament\Resources\DownloadFiles\Schemas;

use App\Models\DownloadFile;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DownloadFileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Kosongkan untuk dibuat otomatis dari judul.'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->label('Kategori')
                    ->maxLength(255)
                    ->datalist(fn (): array => DownloadFile::query()
                        ->whereNotNull('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category')
                        ->all()),
                Toggle::make('is_published')
                    ->label('Dipublikasikan')
                    ->default(true)
                    ->inline(false),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('Berkas')
                    ->collection('file')
                    ->required()
                    ->maxSize(20480)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/vnd.rar',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
