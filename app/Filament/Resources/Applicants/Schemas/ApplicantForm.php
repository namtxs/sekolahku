<?php

namespace App\Filament\Resources\Applicants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Form koreksi data pendaftar oleh admin. Status tidak diubah dari sini,
 * melainkan lewat aksi Verifikasi/Terima/Tolak.
 */
class ApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Diri')
                    ->columns(2)
                    ->schema([
                        TextInput::make('registration_number')
                            ->label('Nomor Pendaftaran')
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('admission_path_id')
                            ->label('Jalur')
                            ->relationship('path', 'name')
                            ->required()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->length(10)
                            ->numeric(),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->required(),
                        TextInput::make('religion')
                            ->label('Agama')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(100),
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->maxDate(now()),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ]),
                Section::make('Orang Tua & Asal Sekolah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('parent_name')
                            ->label('Nama Orang Tua/Wali')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('parent_phone')
                            ->label('No. HP Orang Tua/Wali')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        TextInput::make('school_origin')
                            ->label('Asal Sekolah')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('avg_score')
                            ->label('Rata-rata Nilai Rapor')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('Wajib untuk jalur prestasi.'),
                    ]),
                Section::make('Berkas')
                    ->columns(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->label('Pas Foto')
                            ->collection('photo')
                            ->image()
                            ->maxSize(2048),
                        SpatieMediaLibraryFileUpload::make('documents')
                            ->label('Dokumen (KK, akta, rapor)')
                            ->collection('documents')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(2048),
                    ]),
            ]);
    }
}
