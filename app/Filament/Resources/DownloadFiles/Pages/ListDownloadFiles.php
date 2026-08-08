<?php

namespace App\Filament\Resources\DownloadFiles\Pages;

use App\Filament\Resources\DownloadFiles\DownloadFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDownloadFiles extends ListRecords
{
    protected static string $resource = DownloadFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
