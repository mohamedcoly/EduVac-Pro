<?php

namespace App\Policies;

use App\Models\TeacherDocument;
use App\Models\User;

class TeacherDocumentPolicy
{
    public function view(User $user, TeacherDocument $document): bool
    {
        if (
            $user->hasRole('platform_admin')
            || $document->teacherProfile->user_id === $user->id
        ) {
            return true;
        }

        if (! $user->hasAnyRole(['school_manager', 'school_recruiter'])) {
            return false;
        }

        return $document->applications()
            ->whereHas('jobOffer.school.members', fn ($members) => $members->where('user_id', $user->id))
            ->exists();
    }

    public function delete(User $user, TeacherDocument $document): bool
    {
        return $user->hasRole('platform_admin')
            || $document->teacherProfile->user_id === $user->id;
    }
}
