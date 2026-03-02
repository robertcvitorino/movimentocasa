<?php

namespace App\Models;

use App\Enums\LessonProgressStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberLessonProgress extends Model
{
    /** @use HasFactory<\Database\Factories\MemberLessonProgressFactory> */
    use HasFactory;

    protected $table = 'member_lesson_progress';

    protected $fillable = [
        'member_formation_progress_id',
        'formation_lesson_id',
        'status',
        'started_at',
        'completed_at',
        'last_watched_at',
        'watch_seconds',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_watched_at' => 'datetime',
            'watch_seconds' => 'integer',
            'status' => LessonProgressStatus::class,
        ];
    }

    public function formationProgress(): BelongsTo
    {
        return $this->belongsTo(MemberFormationProgress::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(FormationLesson::class, 'formation_lesson_id');
    }
}
