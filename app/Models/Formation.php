<?php

namespace App\Models;

use App\Enums\FormationApprovalStatus;
use App\Enums\FormationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Formation extends Model
{
    /** @use HasFactory<\Database\Factories\FormationFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'full_description',
        'cover_image_path',
        'ministry_id',
        'is_required',
        'is_general',
        'status',
        'approval_status',
        'approval_notes',
        'reviewed_by',
        'reviewed_at',
        'submitted_for_review_at',
        'certificate_enabled',
        'quiz_enabled',
        'workload_hours',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_required'              => 'boolean',
            'is_general'               => 'boolean',
            'certificate_enabled'      => 'boolean',
            'quiz_enabled'             => 'boolean',
            'published_at'             => 'datetime',
            'reviewed_at'              => 'datetime',
            'submitted_for_review_at'  => 'datetime',
            'minimum_score'            => 'decimal:2',
            'workload_hours'           => 'decimal:2',
            'status'                   => FormationStatus::class,
            'approval_status'          => FormationApprovalStatus::class,
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', FormationStatus::Published);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(FormationLesson::class)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    public function activeLessons(): HasMany
    {
        return $this->lessons()->where('is_active', true);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(MemberFormationProgress::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function quizAttempts(): HasManyThrough
    {
        return $this->hasManyThrough(QuizAttempt::class, Quiz::class);
    }

    protected function lessonsCountLabel(): Attribute
    {
        return Attribute::get(fn (): string => (string) $this->activeLessons()->count());
    }
}
