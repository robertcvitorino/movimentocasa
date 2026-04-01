<?php

namespace App\Services;

use App\Enums\MemberMinistryStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Collection;

class EventAudienceResolver
{
    /**
     * @return Collection<int, User>
     */
    public function resolveUsers(Event $event): Collection
    {
        $event->loadMissing([
            'ministries.members' => fn ($query) => $query
                ->wherePivot('status', MemberMinistryStatus::Active->value)
                ->with('user'),
            'members.user',
        ]);

        $users = collect();

        foreach ($event->ministries as $ministry) {
            $users = $users->merge(
                $ministry->members
                    ->map(fn ($member) => $member->user)
                    ->filter(),
            );
        }

        $users = $users->merge(
            $event->members
                ->map(fn ($member) => $member->user)
                ->filter(),
        );

        return $users
            ->filter(fn (User $user): bool => (bool) $user->is_active)
            ->unique(fn (User $user): int|string => $user->getKey())
            ->values();
    }
}
