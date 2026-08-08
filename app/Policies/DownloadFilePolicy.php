<?php

namespace App\Policies;

use App\Models\DownloadFile;
use App\Models\User;

/**
 * Modul unduhan memakai permission `media.*` sesuai skema permission yang ada.
 */
class DownloadFilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('media.view_any');
    }

    public function view(User $user, DownloadFile $downloadFile): bool
    {
        return $user->can('media.view');
    }

    public function create(User $user): bool
    {
        return $user->can('media.create');
    }

    public function update(User $user, DownloadFile $downloadFile): bool
    {
        return $user->can('media.update');
    }

    public function delete(User $user, DownloadFile $downloadFile): bool
    {
        return $user->can('media.delete');
    }
}
