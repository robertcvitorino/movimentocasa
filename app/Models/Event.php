<?php

namespace App\Models;

use App\Enums\EventAudienceType;
use App\Enums\EventRecurrenceType;
use App\Enums\MemberMinistryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'color',
        'is_recurring',
        'recurrence_type',
        'recurrence_until',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'recurrence_until' => 'date',
            'is_recurring' => 'boolean',
            'recurrence_type' => EventRecurrenceType::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'event_ministry')->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_member')->withTimestamps();
    }

    public function syncAudience(EventAudienceType $audienceType, array $ministryIds = [], array $memberIds = []): void
    {
        match ($audienceType) {
            EventAudienceType::General => $this->syncWithoutRecipients(),
            EventAudienceType::Ministry => $this->syncForMinistries($ministryIds),
            EventAudienceType::Members => $this->syncForMembers($memberIds),
        };
    }

    public function resolveAudienceType(): EventAudienceType
    {
        if ($this->relationLoaded('ministries') ? $this->ministries->isNotEmpty() : $this->ministries()->exists()) {
            return EventAudienceType::Ministry;
        }

        if ($this->relationLoaded('members') ? $this->members->isNotEmpty() : $this->members()->exists()) {
            return EventAudienceType::Members;
        }

        return EventAudienceType::General;
    }

    public function scopeVisibleToMember(Builder $query, Member $member): Builder
    {
        $memberId = $member->getKey();
        $activeMinistryIds = DB::table('member_ministry')
            ->where('member_id', $memberId)
            ->where('status', MemberMinistryStatus::Active->value)
            ->pluck('ministry_id');

        return $query->where(function (Builder $audienceQuery) use ($memberId, $activeMinistryIds): void {
            $audienceQuery->where(function (Builder $generalQuery): void {
                $generalQuery->whereDoesntHave('ministries')
                    ->whereDoesntHave('members');
            });

            if ($activeMinistryIds->isNotEmpty()) {
                $audienceQuery->orWhereHas('ministries', fn (Builder $ministryQuery) => $ministryQuery->whereIn('ministries.id', $activeMinistryIds));
            }

            $audienceQuery->orWhereHas('members', fn (Builder $memberQuery) => $memberQuery->where('members.id', $memberId));
        });
    }

    public function resolveCalendarColor(): string
    {
        if (filled($this->color)) {
            return (string) $this->color;
        }

        if ($this->resolveAudienceType() === EventAudienceType::Ministry) {
            $ministryId = $this->relationLoaded('ministries')
                ? $this->ministries->first()?->getKey()
                : $this->ministries()->value('ministries.id');

            if (filled($ministryId)) {
                $palette = ['#2563eb', '#16a34a', '#ea580c', '#dc2626', '#7c3aed', '#0891b2'];
                $colorIndex = ((int) $ministryId) % count($palette);

                return $palette[$colorIndex];
            }
        }

        if ($this->resolveAudienceType() === EventAudienceType::Members) {
            return '#f59e0b';
        }

        return '#334155';
    }

    protected function syncWithoutRecipients(): void
    {
        $this->ministries()->detach();
        $this->members()->detach();
    }

    protected function syncForMinistries(array $ministryIds): void
    {
        $this->ministries()->sync(collect($ministryIds)->filter()->values()->all());
        $this->members()->detach();
    }

    protected function syncForMembers(array $memberIds): void
    {
        $this->members()->sync(collect($memberIds)->filter()->values()->all());
        $this->ministries()->detach();
    }
}
