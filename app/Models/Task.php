<?php

namespace App\Models;

use App\Enums\MemberMinistryStatus;
use App\Enums\RoleName;
use App\Enums\TaskPriority;
use App\Enums\TaskResponsibleType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'priority',
        'ministry_id',
        'responsible_type',
        'responsible_member_id',
        'responsible_ministry_id',
        'attachment_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'priority' => TaskPriority::class,
            'responsible_type' => TaskResponsibleType::class,
        ];
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function responsibleMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'responsible_member_id');
    }

    public function responsibleMinistry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class, 'responsible_ministry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        $member = $user->member;

        if (! $member) {
            return $query->whereRaw('1 = 0');
        }

        $memberId = $member->getKey();
        $activeMinistryIds = DB::table('member_ministry')
            ->where('member_id', $memberId)
            ->where('status', MemberMinistryStatus::Active->value)
            ->pluck('ministry_id')
            ->all();

        $isCoordinator = $user->hasAnyRole([
            RoleName::SystemAdmin->value,
            RoleName::GeneralCoordinator->value,
            RoleName::MinistryCoordinator->value,
        ]);

        return $query->where(function (Builder $visibilityQuery) use ($memberId, $activeMinistryIds, $isCoordinator): void {
            $visibilityQuery
                ->where(function (Builder $memberTaskQuery) use ($memberId): void {
                    $memberTaskQuery
                        ->where('responsible_type', TaskResponsibleType::Member->value)
                        ->where('responsible_member_id', $memberId);
                })
                ->orWhere(function (Builder $ministryTaskQuery) use ($activeMinistryIds): void {
                    $ministryTaskQuery
                        ->where('responsible_type', TaskResponsibleType::Ministry->value)
                        ->whereIn('responsible_ministry_id', $activeMinistryIds);
                });

            if ($isCoordinator) {
                $visibilityQuery->orWhere('responsible_type', TaskResponsibleType::Member->value);
            }
        });
    }

    public function resolveCalendarColor(): string
    {
        return $this->priority?->color() ?? TaskPriority::Medium->color();
    }
}
