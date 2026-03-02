<?php

namespace App\Models;

use App\Enums\FormationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'status',
        'minimum_score',
        'certificate_enabled',
        'workload_hours',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'certificate_enabled' => 'boolean',
            'published_at' => 'datetime',
            'minimum_score' => 'decimal:2',
            'workload_hours' => 'decimal:2',
            'status' => FormationStatus::class,
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

    public function lessons(): HasMany
    {
        return $this->hasMany(FormationLesson::class)->orderBy('display_order');
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

    protected function lessonsCountLabel(): Attribute
    {
        return Attribute::get(fn (): string => (string) $this->activeLessons()->count());
    }
}
