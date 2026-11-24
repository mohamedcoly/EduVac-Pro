<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'school_manager', 'school_recruiter']);
    }

    public function view(User $user, School $school): bool
    {
        return $user->hasRole('platform_admin')
            || $school->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('school_manager');
    }

    public function update(User $user, School $school): bool
    {
        return $user->hasRole('platform_admin')
            || $school->members()
                ->where('user_id', $user->id)
                ->whereIn('role', ['owner', 'manager'])
                ->exists();
    }

    public function delete(User $user, School $school): bool
    {
        return $user->hasRole('platform_admin')
            || $school->members()
                ->where('user_id', $user->id)
                ->where('role', 'owner')
                ->exists();
    }

    public function verify(User $user, School $school): bool
    {
        return $user->hasRole('platform_admin');
    }
}
