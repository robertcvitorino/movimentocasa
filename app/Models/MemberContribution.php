<?php

namespace App\Models;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberContribution extends Model
{
    /** @use HasFactory<\Database\Factories\MemberContributionFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'reference_month',
        'reference_year',
        'contribution_type',
        'expected_amount',
        'declared_amount',
        'payment_method',
        'status',
        'receipt_path',
        'declared_at',
        'confirmed_at',
        'notes',
        'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'reference_month' => 'integer',
            'reference_year' => 'integer',
            'expected_amount' => 'decimal:2',
            'declared_amount' => 'decimal:2',
            'declared_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'contribution_type' => ContributionType::class,
            'payment_method' => PaymentMethod::class,
            'status' => ContributionStatus::class,
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
