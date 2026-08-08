<?php

namespace App\Filament\Resources\Widgets;

use App\Filament\Resources\Widgets\Pages\CreateWidget;
use App\Filament\Resources\Widgets\Pages\EditWidget;
use App\Filament\Resources\Widgets\Pages\ListWidgets;
use App\Filament\Resources\Widgets\Schemas\WidgetForm;
use App\Filament\Resources\Widgets\Tables\WidgetsTable;
use App\Models\Widget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WidgetResource extends Resource
{
    protected static ?string $model = Widget::class;

    protected static ?string $modelLabel = 'Widget';

    protected static ?string $pluralModelLabel = 'Widget';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static string|UnitEnum|null $navigationGroup = 'Tampilan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return WidgetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WidgetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWidgets::route('/'),
            'create' => CreateWidget::route('/create'),
            'edit' => EditWidget::route('/{record}/edit'),
        ];
    }
}
