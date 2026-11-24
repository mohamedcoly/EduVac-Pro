<?php

namespace App\Policies;

use App\Models\TeacherProfile;
use App\Models\User;

class TeacherProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'school_manager', 'school_recruiter']);
    }

    public function view(User $user, TeacherProfile $profile): bool
    {
        if ($user->hasRole('platform_admin') || $profile->user_id === $user->id) {
            return true;
        }

        if (! $user->hasAnyRole(['school_manager', 'school_recruiter'])) {
            return false;
        }

        if ($profile->verification_status !== 'verified') {
            return false;
        }

        return $user->schoolMemberships()
            ->whereHas('school', fn ($query) => $query->where('verification_status', 'verified'))
            ->exists();
    }

    public function update(User $user, TeacherProfile $profile): bool
    {
        return $user->hasRole('platform_admin') || $profile->user_id === $user->id;
    }

    public function verify(User $user, TeacherProfile $profile): bool
    {
        return $user->hasRole('platform_admin');
    }
}
