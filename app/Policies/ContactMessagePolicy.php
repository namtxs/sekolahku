<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

/**
 * Pesan kontak tidak punya modul permission sendiri; dipakai `settings.*`
 * sehingga hanya Admin (dan Super Admin) yang bisa mengelolanya.
 * Create tidak relevan dari panel (pesan masuk dari form publik).
 */
class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view_any');
    }

    public function view(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('settings.view');
    }

    public function create(User $user): bool
    {
        return $user->can('settings.create');
    }

    public function update(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('settings.update');
    }

    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('settings.delete');
    }
}
