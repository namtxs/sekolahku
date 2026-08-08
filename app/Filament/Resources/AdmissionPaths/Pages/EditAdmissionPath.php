<?php

namespace App\Filament\Resources\AdmissionPaths\Pages;

use App\Filament\Resources\AdmissionPaths\AdmissionPathResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionPath extends EditRecord
{
    protected static string $resource = AdmissionPathResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
