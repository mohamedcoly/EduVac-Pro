<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    protected $fillable = [
        'application_id', 'scheduled_by', 'scheduled_at', 'format',
        'location_or_link', 'internal_notes', 'status', 'teacher_response', 'teacher_response_note', 'responded_at',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'responded_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}
