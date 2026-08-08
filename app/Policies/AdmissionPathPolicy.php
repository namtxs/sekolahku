<?php

namespace App\Policies;

use App\Models\AdmissionPath;
use App\Models\User;

class AdmissionPathPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ppdb.view_any');
    }

    public function view(User $user, AdmissionPath $admissionPath): bool
    {
        return $user->can('ppdb.view');
    }

    public function create(User $user): bool
    {
        return $user->can('ppdb.create');
    }

    public function update(User $user, AdmissionPath $admissionPath): bool
    {
        return $user->can('ppdb.update');
    }

    public function delete(User $user, AdmissionPath $admissionPath): bool
    {
        return $user->can('ppdb.delete');
    }
}
