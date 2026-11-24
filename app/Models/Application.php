<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $fillable = [
        'job_offer_id', 'teacher_profile_id', 'cover_message',
        'teacher_document_id', 'status', 'submitted_at',
    ];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function teacherDocument(): BelongsTo
    {
        return $this->belongsTo(TeacherDocument::class);
    }

    public function mission(): HasOne
    {
        return $this->hasOne(Mission::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('platform_admin')) {
            return $query;
        }

        if ($user->hasRole('teacher')) {
            return $query->whereHas(
                'teacherProfile',
                fn (Builder $profiles) => $profiles->where('user_id', $user->id)
            );
        }

        if ($user->hasAnyRole(['school_manager', 'school_recruiter'])) {
            return $query->whereHas(
                'jobOffer.school.members',
                fn (Builder $members) => $members->where('user_id', $user->id)
            );
        }

        return $query->whereRaw('1 = 0');
    }
}
