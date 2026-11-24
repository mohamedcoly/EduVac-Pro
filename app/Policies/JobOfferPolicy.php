<?php

namespace App\Policies;

use App\Models\JobOffer;
use App\Models\User;

class JobOfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'teacher', 'school_manager', 'school_recruiter']);
    }

    public function view(User $user, JobOffer $offer): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if (
            $user->hasAnyRole(['school_manager', 'school_recruiter'])
            && $offer->school->members()->where('user_id', $user->id)->exists()
        ) {
            return true;
        }

        return $offer->status === 'published'
            && $offer->school->verification_status === 'verified'
            && $user->hasRole('teacher')
            && $user->teacherProfile?->verification_status === 'verified';
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['school_manager', 'school_recruiter'])
            && $user->schoolMemberships()->whereHas(
                'school',
                fn ($query) => $query->where('verification_status', 'verified')
            )->exists();
    }

    public function update(User $user, JobOffer $offer): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $offer->school->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'manager', 'recruiter'])
            ->exists();
    }

    public function publish(User $user, JobOffer $offer): bool
    {
        if (! $this->update($user, $offer)) {
            return false;
        }

        return $offer->school->verification_status === 'verified'
            && in_array($offer->status, ['draft', 'paused'], true);
    }

    public function delete(User $user, JobOffer $offer): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $offer->school->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}
