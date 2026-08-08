<?php

namespace App\Filament\Resources\DownloadFiles;

use App\Filament\Resources\DownloadFiles\Pages\CreateDownloadFile;
use App\Filament\Resources\DownloadFiles\Pages\EditDownloadFile;
use App\Filament\Resources\DownloadFiles\Pages\ListDownloadFiles;
use App\Filament\Resources\DownloadFiles\Schemas\DownloadFileForm;
use App\Filament\Resources\DownloadFiles\Tables\DownloadFilesTable;
use App\Models\DownloadFile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DownloadFileResource extends Resource
{
    protected static ?string $model = DownloadFile::class;

    protected static ?string $modelLabel = 'Unduhan';

    protected static ?string $pluralModelLabel = 'Unduhan';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return DownloadFileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadFilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloadFiles::route('/'),
            'create' => CreateDownloadFile::route('/create'),
            'edit' => EditDownloadFile::route('/{record}/edit'),
        ];
    }
}
