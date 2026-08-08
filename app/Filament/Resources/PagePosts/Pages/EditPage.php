<?php

namespace App\Filament\Resources\PagePosts\Pages;

use App\Filament\Resources\PagePosts\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make()->label('Pulihkan'),
            ForceDeleteAction::make()->label('Hapus Permanen'),
        ];
    }
}
