<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
            SacramentalTitleSeeder::class,
            MinistrySeeder::class,
            MemberSeeder::class,
            PixSettingSeeder::class,
            FormationSeeder::class,
            FinancialGoalSeeder::class,
            MemberContributionSeeder::class,
            MemberJourneySeeder::class,
        ]);
    }
}
