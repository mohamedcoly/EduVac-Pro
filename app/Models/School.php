<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'registration_number',
        'city', 'address', 'verification_status',
    ];

    protected function casts(): array { return ['verified_at' => 'datetime']; }
    public function members(): HasMany { return $this->hasMany(SchoolMember::class); }
    public function jobOffers(): HasMany { return $this->hasMany(JobOffer::class); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class, 'verified_by'); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('platform_admin')) return $query;
        return $query->whereHas('members', fn (Builder $members) => $members->where('user_id', $user->id));
    }
}
