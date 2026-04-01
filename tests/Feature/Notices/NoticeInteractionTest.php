<?php

use App\Models\Member;
use App\Models\Notice;

it('allows member to like and unlike a notice only once', function () {
    $member = Member::factory()->create();
    $notice = Notice::factory()->create();

    $notice->likes()->create([
        'member_id' => $member->getKey(),
    ]);

    expect($notice->likes()->count())->toBe(1);
    expect($notice->isLikedBy($member))->toBeTrue();

    $notice->likes()->where('member_id', $member->getKey())->delete();

    expect($notice->likes()->count())->toBe(0);
    expect($notice->isLikedBy($member))->toBeFalse();
});

it('allows member to comment on a notice', function () {
    $member = Member::factory()->create();
    $notice = Notice::factory()->create();

    $notice->comments()->create([
        'member_id' => $member->getKey(),
        'content' => 'Comentario teste',
    ]);

    $comments = $notice->comments()->pluck('content')->all();

    expect($comments)->toContain('Comentario teste');
});

