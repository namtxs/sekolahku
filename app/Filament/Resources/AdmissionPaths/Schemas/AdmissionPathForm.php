<?php

namespace App\Filament\Resources\AdmissionPaths\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdmissionPathForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jalur')
                    ->columns(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Pelajaran')
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama Jalur')
                            ->placeholder('Zonasi / Prestasi / Afirmasi')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kosongkan untuk dibuat otomatis dari nama. Jalur ber-slug mengandung "prestasi" mewajibkan nilai rapor saat mendaftar.'),
                        TextInput::make('quota')
                            ->label('Kuota')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('requirements')
                            ->label('Syarat Pendaftaran')
                            ->columnSpanFull()
                            ->helperText('Ditampilkan pada kartu jalur di halaman PPDB publik.'),
                    ]),
                Section::make('Periode Pendaftaran')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('opens_at')
                            ->label('Dibuka')
                            ->seconds(false)
                            ->required(),
                        DateTimePicker::make('closes_at')
                            ->label('Ditutup')
                            ->seconds(false)
                            ->required()
                            ->after('opens_at'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Jalur nonaktif tidak menerima pendaftaran meskipun dalam periode.'),
                    ]),
            ]);
    }
}
