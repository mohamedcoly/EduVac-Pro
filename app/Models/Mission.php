<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    protected $fillable = [
        'application_id', 'proposed_by', 'starts_on', 'ends_on', 'weekly_hours',
        'pay_amount_fcfa', 'payment_frequency', 'schedule_details', 'workplace_address',
        'mission_description', 'status', 'teacher_confirmation', 'confirmed_at', 'completion_notes',
    ];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date', 'confirmed_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}