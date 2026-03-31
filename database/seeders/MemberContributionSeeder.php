<?php

namespace Database\Seeders;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Enums\MemberStatus;
use App\Enums\PaymentMethod;
use App\Models\Member;
use App\Models\MemberContribution;
use App\Models\User;
use Illuminate\Database\Seeder;

class MemberContributionSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::query()
            ->where('status', MemberStatus::Active)
            ->get();

        if ($members->isEmpty()) {
            return;
        }

        $financialUserId = User::query()->where('email', 'financeiro@movimentocasa.test')->value('id');

        $referenceDates = [
            now()->subMonths(2),
            now()->subMonth(),
            now(),
        ];

        foreach ($members as $member) {
            foreach ($referenceDates as $date) {
                $month = (int) $date->format('m');
                $year = (int) $date->format('Y');

                $isCurrentMonth = $date->isSameMonth(now());
                $status = $isCurrentMonth ? ContributionStatus::Pending : ContributionStatus::Confirmed;
                $paymentMethod = $isCurrentMonth ? PaymentMethod::Pix : PaymentMethod::Cash;

                MemberContribution::query()->updateOrCreate(
                    [
                        'member_id' => $member->getKey(),
                        'reference_month' => $month,
                        'reference_year' => $year,
                        'contribution_type' => ContributionType::Tithe,
                    ],
                    [
                        'expected_amount' => 80.00,
                        'declared_amount' => $isCurrentMonth ? null : 80.00,
                        'payment_method' => $paymentMethod,
                        'status' => $status,
                        'declared_at' => $isCurrentMonth ? null : $date->copy()->day(10),
                        'confirmed_at' => $isCurrentMonth ? null : $date->copy()->day(11),
                        'notes' => $isCurrentMonth ? 'Aguardando declaração' : 'Contribuição registrada',
                        'confirmed_by' => $isCurrentMonth ? null : $financialUserId,
                    ],
                );

                MemberContribution::query()->updateOrCreate(
                    [
                        'member_id' => $member->getKey(),
                        'reference_month' => $month,
                        'reference_year' => $year,
                        'contribution_type' => ContributionType::Offering,
                    ],
                    [
                        'expected_amount' => 30.00,
                        'declared_amount' => $isCurrentMonth ? null : 35.00,
                        'payment_method' => $paymentMethod,
                        'status' => $isCurrentMonth ? ContributionStatus::Pending : ContributionStatus::Declared,
                        'declared_at' => $isCurrentMonth ? null : $date->copy()->day(12),
                        'confirmed_at' => null,
                        'notes' => 'Oferta mensal',
                        'confirmed_by' => null,
                    ],
                );
            }
        }
    }
}
