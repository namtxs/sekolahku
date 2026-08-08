<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

/**
 * Log aktivitas bersifat baca-saja dari panel: hanya butuh `activity.view_any`
 * (dimiliki Admin; Super Admin lolos via Gate::before). Tidak ada create/
 * update/delete — log tidak boleh diubah dari UI.
 */
class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('activity.view_any');
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->can('activity.view_any');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Activity $activity): bool
    {
        return false;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return false;
    }
}
