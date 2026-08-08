<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ppdb.view_any');
    }

    public function view(User $user, Applicant $applicant): bool
    {
        return $user->can('ppdb.view');
    }

    public function create(User $user): bool
    {
        return $user->can('ppdb.create');
    }

    public function update(User $user, Applicant $applicant): bool
    {
        return $user->can('ppdb.update');
    }

    public function delete(User $user, Applicant $applicant): bool
    {
        return $user->can('ppdb.delete');
    }
}
