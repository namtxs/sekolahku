<?php

namespace App\Filament\Resources\AdmissionPaths\Pages;

use App\Filament\Resources\AdmissionPaths\AdmissionPathResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionPaths extends ListRecords
{
    protected static string $resource = AdmissionPathResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
