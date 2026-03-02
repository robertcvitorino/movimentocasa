<?php

namespace App\Filament\Resources\FinancialGoals\Pages;

use App\Filament\Resources\FinancialGoals\FinancialGoalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialGoal extends CreateRecord
{
    protected static string $resource = FinancialGoalResource::class;
}
