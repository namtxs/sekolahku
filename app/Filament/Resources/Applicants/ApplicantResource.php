<?php

namespace App\Filament\Resources\Applicants;

use App\Filament\Resources\Applicants\Pages\EditApplicant;
use App\Filament\Resources\Applicants\Pages\ListApplicants;
use App\Filament\Resources\Applicants\Pages\ViewApplicant;
use App\Filament\Resources\Applicants\Schemas\ApplicantForm;
use App\Filament\Resources\Applicants\Schemas\ApplicantInfolist;
use App\Filament\Resources\Applicants\Tables\ApplicantsTable;
use App\Models\Applicant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $modelLabel = 'Pendaftar';

    protected static ?string $pluralModelLabel = 'Pendaftar';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'PPDB';

    protected static ?int $navigationSort = 3;

    /**
     * Pendaftar hanya masuk lewat formulir publik; admin tidak membuat manual.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ApplicantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplicants::route('/'),
            'view' => ViewApplicant::route('/{record}'),
            'edit' => EditApplicant::route('/{record}/edit'),
        ];
    }
}
