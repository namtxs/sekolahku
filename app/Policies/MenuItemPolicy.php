<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;

/**
 * Item menu memakai permission modul `menus.*` yang sama dengan Menu.
 */
class MenuItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('menus.view_any');
    }

    public function view(User $user, MenuItem $menuItem): bool
    {
        return $user->can('menus.view');
    }

    public function create(User $user): bool
    {
        return $user->can('menus.create');
    }

    public function update(User $user, MenuItem $menuItem): bool
    {
        return $user->can('menus.update');
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        return $user->can('menus.delete');
    }
}
