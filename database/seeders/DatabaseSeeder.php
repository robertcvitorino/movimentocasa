<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Permissões e roles (Shield)
            ShieldSeeder::class,

            // 2. Super Admin
            SuperAdminSeeder::class,

            // 3. Ministérios — um seeder por ministério
            MinistryAcolhidaSeeder::class,
            MinistryLouvorSeeder::class,
            MinistryFormacaoSeeder::class,
        ]);
    }
}
