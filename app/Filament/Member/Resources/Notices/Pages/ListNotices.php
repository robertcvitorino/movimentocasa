<?php

namespace App\Filament\Member\Resources\Notices\Pages;

use App\Filament\Member\Resources\Notices\NoticeResource;
use App\Models\Notice;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListNotices extends ListRecords
{
    protected static string $resource = NoticeResource::class;

    protected string $view = 'filament.member.resources.notices.pages.list-notices-feed';

    /**
     * @var array<int, string>
     */
    public array $commentInputs = [];

    /**
     * @var array<int, bool>
     */
    public array $commentFormVisible = [];

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getFeedNotices(): Collection
    {
        $memberId = auth()->user()?->member?->getKey();

        return Notice::query()
            ->visibleToMember()
            ->with('creator')
            ->withCount(['likes', 'comments'])
            ->with([
                'likes' => fn ($query) => $query->where('member_id', $memberId),
                'comments' => fn ($query) => $query->with('member')->latest()->limit(8),
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
    }

    public function toggleLike(int $noticeId): void
    {
        $member = auth()->user()?->member;

        if (! $member) {
            return;
        }

        $notice = Notice::query()
            ->visibleToMember()
            ->findOrFail($noticeId);

        $existingLike = $notice->likes()
            ->where('member_id', $member->getKey())
            ->first();

        if ($existingLike) {
            $existingLike->delete();

            return;
        }

        $notice->likes()->create([
            'member_id' => $member->getKey(),
        ]);
    }

    public function toggleCommentForm(int $noticeId): void
    {
        $this->commentFormVisible[$noticeId] = ! ($this->commentFormVisible[$noticeId] ?? false);
    }

    public function submitComment(int $noticeId): void
    {
        $member = auth()->user()?->member;

        if (! $member) {
            return;
        }

        $content = trim((string) ($this->commentInputs[$noticeId] ?? ''));

        if ($content === '') {
            Notification::make()
                ->title('Escreva um comentario antes de enviar.')
                ->warning()
                ->send();

            return;
        }

        $notice = Notice::query()
            ->visibleToMember()
            ->findOrFail($noticeId);

        $notice->comments()->create([
            'member_id' => $member->getKey(),
            'content' => $content,
        ]);

        $this->commentInputs[$noticeId] = '';
        $this->commentFormVisible[$noticeId] = false;

        Notification::make()
            ->title('Comentario publicado.')
            ->success()
            ->send();
    }
}
