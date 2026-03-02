<?php

namespace Database\Seeders;

use App\Models\SacramentalTitle;
use Illuminate\Database\Seeder;

class SacramentalTitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['name' => 'Batismo', 'slug' => 'baptism', 'type' => 'sacrament'],
            ['name' => 'Primeira Eucaristia', 'slug' => 'first-eucharist', 'type' => 'sacrament'],
            ['name' => 'Crisma', 'slug' => 'confirmation', 'type' => 'sacrament'],
            ['name' => 'Matrimônio', 'slug' => 'matrimony', 'type' => 'sacrament'],
            ['name' => 'Outro', 'slug' => 'other', 'type' => 'other'],
        ];

        foreach ($titles as $index => $title) {
            SacramentalTitle::query()->updateOrCreate(
                ['slug' => $title['slug']],
                [...$title, 'is_active' => true, 'sort_order' => $index + 1],
            );
        }
    }
}
