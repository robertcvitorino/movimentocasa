<?php

namespace App\Filament\Member\Widgets;

use App\Models\MemberFormationProgress;
use App\Models\MemberContribution;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemberJourneyOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $memberId = auth()->user()?->member?->getKey();

        if (! $memberId) {
            return [];
        }

        return [
            Stat::make('Formações em andamento', (string) MemberFormationProgress::query()->where('member_id', $memberId)->where('status', 'in_progress')->count()),
            Stat::make('Formações concluídas', (string) MemberFormationProgress::query()->where('member_id', $memberId)->where('status', 'completed')->count()),
            Stat::make('Contribuições pendentes', (string) MemberContribution::query()->where('member_id', $memberId)->where('status', 'pending')->count()),
        ];
    }
}
