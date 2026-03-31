<?php

namespace Database\Seeders;

use App\Enums\FinancialGoalStatus;
use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinancialGoalSeeder extends Seeder
{
    public function run(): void
    {
        $financialUserId = User::query()->where('email', 'financeiro@movimentocasa.test')->value('id')
            ?? User::query()->where('email', 'admin@admin.com')->value('id');

        $goals = [
            [
                'title' => 'Meta de manutenção mensal',
                'description' => 'Custos operacionais e manutenção da casa.',
                'target_amount' => 8500,
                'month' => now()->month,
                'year' => now()->year,
                'status' => FinancialGoalStatus::Active,
            ],
            [
                'title' => 'Campanha solidária de inverno',
                'description' => 'Arrecadação para ações sociais e cestas básicas.',
                'target_amount' => 5000,
                'month' => now()->addMonth()->month,
                'year' => now()->addMonth()->year,
                'status' => FinancialGoalStatus::Draft,
            ],
            [
                'title' => 'Reforma do salão principal',
                'description' => 'Adequações estruturais e equipamentos.',
                'target_amount' => 18000,
                'month' => now()->subMonth()->month,
                'year' => now()->subMonth()->year,
                'status' => FinancialGoalStatus::Completed,
            ],
        ];

        foreach ($goals as $goal) {
            FinancialGoal::query()->updateOrCreate(
                [
                    'title' => $goal['title'],
                    'month' => $goal['month'],
                    'year' => $goal['year'],
                ],
                [
                    'description' => $goal['description'],
                    'target_amount' => $goal['target_amount'],
                    'status' => $goal['status'],
                    'created_by' => $financialUserId,
                    'updated_by' => $financialUserId,
                ],
            );
        }
    }
}
