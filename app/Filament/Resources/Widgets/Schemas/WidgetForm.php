<?php

namespace App\Filament\Resources\Widgets\Schemas;

use App\Enums\WidgetArea;
use App\Models\Widget;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class WidgetForm
{
    /**
     * Tipe widget yang memakai pengaturan jumlah item.
     *
     * @var array<int, string>
     */
    protected static array $limitedTypes = [
        'recent_posts',
        'popular_posts',
        'categories',
        'tags',
        'archive',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Tampil sebagai judul kotak widget di situs publik.'),
                Select::make('type')
                    ->label('Tipe')
                    ->options(Widget::TYPES)
                    ->required()
                    ->live()
                    ->native(false)
                    ->helperText('Tipe Pencarian, Media Sosial, dan Info Kontak mengambil data dari Pengaturan Umum tanpa isian tambahan.'),
                Select::make('area')
                    ->label('Area')
                    ->options(WidgetArea::options())
                    ->default(WidgetArea::Sidebar->value)
                    ->required()
                    ->native(false)
                    ->helperText('Sidebar Berita tampil di beranda dan halaman berita; Sidebar Halaman Statis tampil di halaman seperti profil sekolah.'),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),

                RichEditor::make('content')
                    ->label('Konten')
                    ->visible(fn (Get $get): bool => $get('type') === 'text')
                    ->requiredIf('type', 'text')
                    ->helperText('Isi teks/HTML yang ditampilkan di dalam widget.')
                    ->columnSpanFull(),

                TextInput::make('settings.limit')
                    ->label('Jumlah Item')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(50)
                    ->placeholder('Bawaan: 5–20 tergantung tipe')
                    ->visible(fn (Get $get): bool => in_array($get('type'), static::$limitedTypes, true))
                    ->helperText('Banyaknya item yang ditampilkan widget.'),

                Repeater::make('settings.links')
                    ->label('Daftar Tautan')
                    ->visible(fn (Get $get): bool => $get('type') === 'links')
                    ->schema([
                        TextInput::make('label')
                            ->label('Label')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->maxLength(255)
                            ->regex('~^(https?://|/)~')
                            ->validationMessages([
                                'regex' => 'Tautan harus berupa URL lengkap (diawali http:// atau https://) atau path relatif yang diawali garis miring (/).',
                            ]),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->addActionLabel('Tambah Tautan')
                    ->columnSpanFull(),
            ]);
    }
}
