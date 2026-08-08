<?php

namespace App\Filament\Resources\AdmissionPaths;

use App\Filament\Resources\AdmissionPaths\Pages\CreateAdmissionPath;
use App\Filament\Resources\AdmissionPaths\Pages\EditAdmissionPath;
use App\Filament\Resources\AdmissionPaths\Pages\ListAdmissionPaths;
use App\Filament\Resources\AdmissionPaths\Schemas\AdmissionPathForm;
use App\Filament\Resources\AdmissionPaths\Tables\AdmissionPathsTable;
use App\Models\AdmissionPath;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdmissionPathResource extends Resource
{
    protected static ?string $model = AdmissionPath::class;

    protected static ?string $modelLabel = 'Jalur PPDB';

    protected static ?string $pluralModelLabel = 'Jalur PPDB';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsPointingIn;

    protected static string|UnitEnum|null $navigationGroup = 'PPDB';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AdmissionPathForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionPathsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmissionPaths::route('/'),
            'create' => CreateAdmissionPath::route('/create'),
            'edit' => EditAdmissionPath::route('/{record}/edit'),
        ];
    }
}
