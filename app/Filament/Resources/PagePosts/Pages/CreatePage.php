<?php

namespace App\Filament\Resources\PagePosts\Pages;

use App\Filament\Resources\PagePosts\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected static ?string $title = 'Buat Halaman Baru';

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Halaman berhasil disimpan.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'page';
        $data['user_id'] = auth()->id();

        return $data;
    }
}
