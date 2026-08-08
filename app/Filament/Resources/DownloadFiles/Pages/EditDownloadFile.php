<?php

namespace App\Filament\Resources\DownloadFiles\Pages;

use App\Filament\Resources\DownloadFiles\DownloadFileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDownloadFile extends EditRecord
{
    protected static string $resource = DownloadFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
