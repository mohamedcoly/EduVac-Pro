<?php

namespace App\Policies;

use App\Models\Interview;
use App\Models\User;

class InterviewPolicy
{
    public function view(User $user, Interview $interview): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        $application = $interview->application;

        if ($application->teacherProfile->user_id === $user->id) {
            return true;
        }

        return $application->jobOffer->school->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function schedule(User $user, Interview $interview): bool
    {
        return $user->hasAnyRole(['platform_admin', 'school_manager', 'school_recruiter'])
            && ($user->hasRole('platform_admin')
                || $interview->application->jobOffer->school->members()
                    ->where('user_id', $user->id)
                    ->whereIn('role', ['owner', 'manager', 'recruiter'])
                    ->exists());
    }

    public function respond(User $user, Interview $interview): bool
    {
        return $user->hasRole('teacher')
            && $interview->application->teacherProfile->user_id === $user->id;
    }
}