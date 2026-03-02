<?php

namespace App\Models;

use App\Enums\FormationProgressStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MemberFormationProgress extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFormationProgressFactory> */
    use HasFactory;

    protected $table = 'member_formation_progress';

    protected $fillable = [
        'member_id',
        'formation_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at',
        'last_accessed_at',
        'required_lessons_count',
        'completed_required_lessons_count',
        'quiz_score',
        'quiz_passed_at',
        'certificate_issued_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'quiz_score' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'quiz_passed_at' => 'datetime',
            'certificate_issued_at' => 'datetime',
            'status' => FormationProgressStatus::class,
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

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(MemberLessonProgress::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
