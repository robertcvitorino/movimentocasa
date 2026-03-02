<?php

namespace App\Models;

use App\Enums\FinancialGoalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialGoal extends Model
{
    /** @use HasFactory<\Database\Factories\FinancialGoalFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'month',
        'year',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
            'status' => FinancialGoalStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
