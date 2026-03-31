<?php

namespace Database\Seeders;

use App\Models\PixSetting;
use Illuminate\Database\Seeder;

class PixSettingSeeder extends Seeder
{
    public function run(): void
    {
        PixSetting::query()->updateOrCreate(
            ['pix_key' => 'financeiro@movimentocasa.test'],
            [
                'beneficiary_name' => 'Movimento Casa',
                'city' => 'São Paulo',
                'copy_paste_code' => '00020126580014br.gov.bcb.pix0136financeiro@movimentocasa.test5204000053039865802BR5925Movimento Casa6009SAO PAULO62070503***6304ABCD',
                'instructions' => 'Use esta chave para dízimo, oferta e doações. Envie comprovante na plataforma quando necessário.',
                'is_active' => true,
            ],
        );
    }
}
