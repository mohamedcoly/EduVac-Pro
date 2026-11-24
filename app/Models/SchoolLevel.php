<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolLevel extends Model
{
    protected $fillable = ['name', 'slug'];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(TeacherProfile::class, 'teacher_school_level');
    }
}
