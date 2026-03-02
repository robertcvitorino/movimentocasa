<?php

namespace App\Models;

use App\Enums\LessonSourceType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormationLesson extends Model
{
    /** @use HasFactory<\Database\Factories\FormationLessonFactory> */
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'title',
        'description',
        'support_text',
        'source_type',
        'video_url',
        'video_path',
        'support_document_path',
        'support_document_paths',
        'display_order',
        'estimated_duration_minutes',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'estimated_duration_minutes' => 'integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'source_type' => LessonSourceType::class,
            'support_document_paths' => 'array',
        ];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(MemberLessonProgress::class);
    }

    protected function supportDocuments(): Attribute
    {
        return Attribute::get(function (): array {
            $documents = $this->support_document_paths ?? [];

            if (! empty($documents)) {
                return array_values(array_filter($documents));
            }

            return $this->support_document_path ? [$this->support_document_path] : [];
        });
    }
}
