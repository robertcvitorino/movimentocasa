<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    /** @use HasFactory<\Database\Factories\CertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'formation_id',
        'member_formation_progress_id',
        'certificate_code',
        'issued_at',
        'pdf_path',
        'verification_hash',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function formationProgress(): BelongsTo
    {
        return $this->belongsTo(MemberFormationProgress::class, 'member_formation_progress_id');
    }
}
