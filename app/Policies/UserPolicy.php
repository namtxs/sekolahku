<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view_any');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.update');
    }

    /**
     * User tidak boleh menghapus dirinya sendiri.
     * Catatan: Super Admin lolos via Gate::before, jadi larangan hapus diri
     * sendiri juga dijaga di level resource (aksi disembunyikan).
     */
    public function delete(User $user, User $model): bool
    {
        return $model->id !== $user->id && $user->can('users.delete');
    }
}
