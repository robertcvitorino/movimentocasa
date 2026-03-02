<?php

namespace App\Filament\Widgets;

use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberContribution;
use App\Models\Ministry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Membros ativos', (string) Member::query()->where('status', 'active')->count()),
            Stat::make('Ministérios ativos', (string) Ministry::query()->where('status', 'active')->count()),
            Stat::make('Formações publicadas', (string) Formation::query()->where('status', 'published')->count()),
            Stat::make('Contribuições pendentes', (string) MemberContribution::query()->where('status', 'pending')->count()),
        ];
    }
}
