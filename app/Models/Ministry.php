<?php

namespace App\Models;

use App\Enums\MemberMinistryStatus;
use App\Enums\MinistryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ministry extends Model
{
    /** @use HasFactory<\Database\Factories\MinistryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => MinistryStatus::class,
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_ministry')
            ->withPivot(['id', 'role_name', 'status', 'joined_at', 'left_at', 'notes'])
            ->withPivotValue('status', MemberMinistryStatus::Active->value)
            ->withTimestamps();
    }

    public function coordinators(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'ministry_coordinators')
            ->withPivot(['id', 'is_primary', 'appointed_at', 'ended_at'])
            ->withTimestamps();
    }

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_ministry')->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'responsible_ministry_id');
    }
}
