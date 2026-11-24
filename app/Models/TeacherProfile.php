<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'phone',
        'city', 'years_experience', 'verification_status',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'years_experience' => 'integer',
            'profile_completion' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TeacherDocument::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('platform_admin')) {
            return $query;
        }

        if ($user->hasRole('teacher')) {
            return $query->where('user_id', $user->id);
        }

        if (
            $user->hasAnyRole(['school_manager', 'school_recruiter'])
            && $user->schoolMemberships()->whereHas(
                'school',
                fn (Builder $schools) => $schools->where('verification_status', 'verified')
            )->exists()
        ) {
            return $query->where('verification_status', 'verified');
        }

        return $query->whereRaw('1 = 0');
    }
}
