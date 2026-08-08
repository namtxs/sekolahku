<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Tulis Berita Baru';

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Berita berhasil disimpan.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'post';
        $data['user_id'] = auth()->id();

        return $data;
    }
}
