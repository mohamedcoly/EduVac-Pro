<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOffer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'created_by', 'title', 'description', 'subject_id',
        'school_level_id', 'city', 'address', 'latitude', 'longitude',
        'weekly_hours', 'salary_min_fcfa', 'salary_max_fcfa',
        'starts_on', 'application_deadline', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'application_deadline' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function schoolLevel(): BelongsTo { return $this->belongsTo(SchoolLevel::class); }
    public function applications(): HasMany { return $this->hasMany(Application::class); }

    public function isOpenForApplications(): bool
    {
        $deadline = $this->application_deadline;
        return $this->status === 'published'
            && $this->school?->verification_status === 'verified'
            && ($deadline === null || $deadline->isToday() || $deadline->isFuture());
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('platform_admin')) return $query;
        if ($user->hasAnyRole(['school_manager', 'school_recruiter'])) {
            return $query->whereHas('school.members', fn (Builder $members) => $members->where('user_id', $user->id));
        }
        if ($user->hasRole('teacher') && $user->teacherProfile?->verification_status === 'verified') {
            return $query->where('status', 'published')
                ->whereHas('school', fn (Builder $schools) => $schools->where('verification_status', 'verified'));
        }
        return $query->whereRaw('1 = 0');
    }
}
