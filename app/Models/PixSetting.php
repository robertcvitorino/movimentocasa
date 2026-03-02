<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PixSetting extends Model
{
    /** @use HasFactory<\Database\Factories\PixSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'pix_key',
        'beneficiary_name',
        'city',
        'qr_code_image_path',
        'copy_paste_code',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
