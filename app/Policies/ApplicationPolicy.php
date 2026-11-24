<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'teacher', 'school_manager', 'school_recruiter']);
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($application->teacherProfile->user_id === $user->id) {
            return true;
        }

        return $application->jobOffer->school->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function create(User $user, JobOffer $offer): bool
    {
        $profile = $user->teacherProfile;

        return $user->hasRole('teacher')
            && $profile !== null
            && $profile->verification_status === 'verified'
            && $profile->is_available
            && $offer->isOpenForApplications();
    }

    public function manage(User $user, Application $application): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $application->jobOffer->school->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'manager', 'recruiter'])
            ->exists();
    }

    public function withdraw(User $user, Application $application): bool
    {
        return $application->teacherProfile->user_id === $user->id
            && in_array($application->status, ['submitted', 'shortlisted'], true);
    }
}
