<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageGeneralSettings extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static ?string $title = 'Pengaturan Umum';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('settings.view_any') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Sekolah')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nama Sekolah / Situs')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama resmi lengkap, misalnya SMK Muhammadiyah 1 Ambarawa.'),
                        TextInput::make('site_short_name')
                            ->label('Nama Singkat / Brand')
                            ->maxLength(100)
                            ->helperText('Ditampilkan menonjol di header (contoh: SMK MUSAMBA). Kosongkan jika tidak dipakai.'),
                        TextInput::make('site_tagline')
                            ->label('Tagline / Slogan')
                            ->maxLength(150)
                            ->helperText('Motto singkat di bawah nama sekolah pada header (contoh: Beriman, Berilmu, Berprestasi). Kosongkan jika tidak dipakai.')
                            ->columnSpanFull(),
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(20),
                        Select::make('jenjang')
                            ->label('Jenjang')
                            ->options([
                                'SD' => 'SD',
                                'SMP' => 'SMP',
                                'SMA' => 'SMA',
                                'SMK' => 'SMK',
                                'lainnya' => 'Lainnya',
                            ]),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(30),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
                Section::make('Kepala Sekolah')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('headmaster_name')
                            ->label('Nama Kepala Sekolah')
                            ->maxLength(255),
                        Textarea::make('headmaster_greeting')
                            ->label('Sambutan Kepala Sekolah')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Section::make('Media Sosial')
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('youtube')
                            ->label('YouTube')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('twitter')
                            ->label('Twitter / X')
                            ->url()
                            ->maxLength(255),
                    ]),
                Section::make('SEO')
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2),
                        Textarea::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->rows(2)
                            ->helperText('Pisahkan dengan koma.'),
                    ]),
                Section::make('Tema')
                    ->columnSpanFull()
                    ->collapsed()
                    ->schema([
                        Select::make('active_theme')
                            ->label('Tema Aktif')
                            ->options([
                                'default' => 'Default',
                            ])
                            ->required(),
                    ]),
            ]);
    }
}
