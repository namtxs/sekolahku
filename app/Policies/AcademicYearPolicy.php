<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;

class AcademicYearPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ppdb.view_any');
    }

    public function view(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('ppdb.view');
    }

    public function create(User $user): bool
    {
        return $user->can('ppdb.create');
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('ppdb.update');
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('ppdb.delete');
    }
}
