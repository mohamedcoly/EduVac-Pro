<?php

namespace App\Policies;

use App\Models\Mission;
use App\Models\User;

class MissionPolicy
{
    public function view(User $user, Mission $mission): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($mission->application->teacherProfile->user_id === $user->id) {
            return true;
        }

        return $mission->application->jobOffer->school->members()
            ->where('user_id', $user->id)->exists();
    }

    public function propose(User $user, Mission $mission): bool
    {
        return $user->hasAnyRole(['platform_admin', 'school_manager', 'school_recruiter'])
            && ($user->hasRole('platform_admin')
                || $mission->application->jobOffer->school->members()
                    ->where('user_id', $user->id)
                    ->whereIn('role', ['owner', 'manager', 'recruiter'])
                    ->exists());
    }

    public function respond(User $user, Mission $mission): bool
    {
        return $user->hasRole('teacher')
            && $mission->application->teacherProfile->user_id === $user->id;
    }
}