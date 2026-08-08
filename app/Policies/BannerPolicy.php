<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('banners.view_any');
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->can('banners.view');
    }

    public function create(User $user): bool
    {
        return $user->can('banners.create');
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->can('banners.update');
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('banners.delete');
    }
}
