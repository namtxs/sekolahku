<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Widget;

class WidgetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('widgets.view_any');
    }

    public function view(User $user, Widget $widget): bool
    {
        return $user->can('widgets.view');
    }

    public function create(User $user): bool
    {
        return $user->can('widgets.create');
    }

    public function update(User $user, Widget $widget): bool
    {
        return $user->can('widgets.update');
    }

    public function delete(User $user, Widget $widget): bool
    {
        return $user->can('widgets.delete');
    }
}
