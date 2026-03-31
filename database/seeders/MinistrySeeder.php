<?php

namespace Database\Seeders;

use App\Enums\MinistryStatus;
use App\Models\Ministry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MinistrySeeder extends Seeder
{
    public function run(): void
    {
        $ministries = [
            [
                'name' => 'Acolhida',
                'description' => 'Recepção e acolhimento dos participantes nos encontros.',
                'status' => MinistryStatus::Active,
            ],
            [
                'name' => 'Intercessão',
                'description' => 'Serviço de oração e intercessão pelas intenções da comunidade.',
                'status' => MinistryStatus::Active,
            ],
            [
                'name' => 'Formação',
                'description' => 'Organização de trilhas formativas e conteúdo catequético.',
                'status' => MinistryStatus::Active,
            ],
            [
                'name' => 'Música',
                'description' => 'Serviço de música e animação litúrgica.',
                'status' => MinistryStatus::Active,
            ],
            [
                'name' => 'Ação Social',
                'description' => 'Projetos de solidariedade e apoio comunitário.',
                'status' => MinistryStatus::Inactive,
            ],
        ];

        foreach ($ministries as $index => $ministry) {
            Ministry::query()->updateOrCreate(
                ['slug' => Str::slug($ministry['name'])],
                [
                    'name' => $ministry['name'],
                    'description' => $ministry['description'],
                    'status' => $ministry['status'],
                    'updated_at' => now()->addSeconds($index),
                ],
            );
        }
    }
}
