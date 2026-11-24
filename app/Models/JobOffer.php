<?php

namespace App\Models;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isOpenForApplications(): bool
    {
        $deadline = $this->application_deadline;

        return $this->status === 'published'
            && ($deadline === null || $deadline->isToday() || $deadline->isFuture());
    }
}
