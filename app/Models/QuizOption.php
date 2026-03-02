<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOption extends Model
{
    /** @use HasFactory<\Database\Factories\QuizOptionFactory> */
    use HasFactory;

    protected $fillable = [
        'quiz_question_id',
        'label',
        'is_correct',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
