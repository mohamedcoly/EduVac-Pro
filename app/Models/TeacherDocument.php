<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherDocument extends Model
{
    protected $fillable = [
        'teacher_profile_id', 'type', 'disk', 'path', 'original_name',
        'mime_type', 'size_bytes', 'verification_status',
    ];

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
