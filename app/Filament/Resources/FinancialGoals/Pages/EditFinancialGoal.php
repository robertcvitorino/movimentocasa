<?php

namespace App\Filament\Resources\FinancialGoals\Pages;

use App\Filament\Resources\FinancialGoals\FinancialGoalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialGoal extends EditRecord
{
    protected static string $resource = FinancialGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
