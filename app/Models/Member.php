<?php

namespace App\Models;

use App\Enums\MemberMinistryStatus;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'birth_date',
        'email',
        'phone',
        'is_whatsapp',
        'instagram',
        'zip_code',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'status',
        'joined_at',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_whatsapp' => 'boolean',
            'joined_at' => 'date',
            'status' => MemberStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(SacramentalTitle::class, 'member_titles')
            ->withPivot(['id', 'notes', 'received_at'])
            ->withTimestamps();
    }

    public function ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'member_ministry')
            ->withPivot(['id', 'role_name', 'status', 'joined_at', 'left_at', 'notes'])
            ->withPivotValue('status', MemberMinistryStatus::Active->value)
            ->withTimestamps();
    }

    public function coordinatedMinistries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'ministry_coordinators')
            ->withPivot(['id', 'is_primary', 'appointed_at', 'ended_at'])
            ->withTimestamps();
    }

    public function formationProgress(): HasMany
    {
        return $this->hasMany(MemberFormationProgress::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(MemberContribution::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_member')->withTimestamps();
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'responsible_member_id');
    }
}
