<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tahun Pelajaran')
                    ->placeholder('2026/2027')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->regex('~^\d{4}/\d{4}$~')
                    ->validationMessages([
                        'regex' => 'Format tahun pelajaran harus seperti 2026/2027.',
                    ]),
                Toggle::make('is_active')
                    ->label('Jadikan Tahun Aktif')
                    ->inline(false)
                    ->helperText('Hanya satu tahun pelajaran yang aktif. Mengaktifkan tahun ini otomatis menonaktifkan tahun lainnya.'),
            ]);
    }
}
