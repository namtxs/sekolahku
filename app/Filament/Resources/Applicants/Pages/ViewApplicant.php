<?php

namespace App\Filament\Resources\Applicants\Pages;

use App\Filament\Resources\Applicants\ApplicantActions;
use App\Filament\Resources\Applicants\ApplicantResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicant extends ViewRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApplicantActions::verify(),
            ApplicantActions::accept(),
            ApplicantActions::reject(),
            EditAction::make(),
        ];
    }
}
