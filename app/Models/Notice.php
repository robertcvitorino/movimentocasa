<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notice extends Model
{
    /** @use HasFactory<\Database\Factories\NoticeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'cover_image_path',
        'content',
        'is_published',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Notice $notice): void {
            if ($notice->is_published && blank($notice->published_at)) {
                $notice->published_at = now();
            }

            if (! $notice->is_published) {
                $notice->published_at = null;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(NoticeLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(NoticeComment::class)->where('is_hidden', false);
    }

    public function scopeVisibleToMember(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $publishQuery): void {
                $publishQuery
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function isLikedBy(Member $member): bool
    {
        return $this->likes()->where('member_id', $member->getKey())->exists();
    }
}
