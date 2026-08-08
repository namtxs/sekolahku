<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Kolom utama: konten + SEO.
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Konten')
                            ->description('Judul, ringkasan, dan isi berita.')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Kosongkan untuk dibuat otomatis dari judul.'),
                                Textarea::make('excerpt')
                                    ->label('Ringkasan')
                                    ->rows(3)
                                    ->maxLength(300)
                                    ->helperText('Ditampilkan pada daftar berita dan hasil pencarian. Idealnya 120–160 karakter.'),
                                RichEditor::make('body')
                                    ->label('Isi')
                                    ->required(),
                            ]),
                        Section::make('SEO')
                            ->description('Pengaturan judul dan deskripsi untuk mesin pencari.')
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(255)
                                    ->helperText('Kosongkan untuk memakai judul berita.'),
                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(2)
                                    ->helperText('Kosongkan untuk memakai ringkasan.'),
                            ]),
                    ]),

                // Sidebar meta: publikasi, kategori, gambar.
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Publikasi')
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draf',
                                        'published' => 'Terbit',
                                        'scheduled' => 'Terjadwal',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->live(),
                                DateTimePicker::make('published_at')
                                    ->label('Waktu Terbit')
                                    ->seconds(false)
                                    ->required(fn (Get $get): bool => $get('status') === 'scheduled')
                                    ->helperText('Wajib diisi bila status Terjadwal.'),
                                Toggle::make('allow_comments')
                                    ->label('Izinkan Komentar')
                                    ->default(true)
                                    ->inline(false)
                                    ->helperText('Matikan untuk menutup kolom komentar pada berita ini.'),
                            ]),
                        Section::make('Klasifikasi')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Select::make('tags')
                                    ->label('Tag')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ]),
                        Section::make('Gambar Utama')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('featured')
                                    ->label('Gambar Utama')
                                    ->collection('featured')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->helperText('Maksimal 5 MB. Ukuran ideal 1200×630 piksel (rasio berbagi media sosial).'),
                            ]),
                    ]),
            ]);
    }
}
