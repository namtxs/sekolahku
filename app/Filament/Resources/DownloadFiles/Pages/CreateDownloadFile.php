<?php

namespace App\Filament\Resources\DownloadFiles\Pages;

use App\Filament\Resources\DownloadFiles\DownloadFileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDownloadFile extends CreateRecord
{
    protected static string $resource = DownloadFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
