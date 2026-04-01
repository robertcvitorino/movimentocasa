<x-filament-panels::page>
    <style>
        .notice-feed {
            width: 100%;
            max-width: none;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .notice-card {
            width: 100%;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            padding: 12px;
        }

        .notice-header {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 10px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            letter-spacing: 0.01em;
            color: #111827;
        }

        .notice-cover {
            width: 280px;
            height: 280px;
            margin: 0 auto 12px auto;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .notice-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .notice-meta-card,
        .notice-content-card,
        .notice-actions-card,
        .notice-comments-card,
        .notice-comment-form-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px;
            margin-top: 10px;
        }

        .notice-author {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .notice-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fef3c7;
            color: #92400e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .notice-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .notice-author-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            line-height: 1.2;
        }

        .notice-author-date {
            font-size: 12px;
            color: #6b7280;
            margin: 2px 0 0 0;
            line-height: 1.2;
        }

        .notice-content {
            white-space: pre-line;
            font-size: 14px;
            line-height: 1.45;
            color: #1f2937;
            margin: 0;
        }

        .notice-stats {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #4b5563;
        }

        .notice-stats-like {
            color: #2563eb;
            font-weight: 600;
        }

        .notice-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px;
        }

        .notice-action-btn {
            border: 0;
            border-radius: 8px;
            background: #fff;
            padding: 7px 6px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
        }

        .notice-action-btn:hover {
            background: #f3f4f6;
        }

        .notice-comments-title {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .notice-comment-item {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .notice-comment-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .notice-comment-author {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .notice-comment-date {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }

        .notice-comment-text {
            margin: 0;
            white-space: pre-line;
            font-size: 13px;
            line-height: 1.4;
            color: #374151;
        }

        .notice-empty-comments {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }

        .notice-comment-label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
        }

        .notice-comment-textarea {
            width: 100%;
            min-height: 78px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px;
            font-size: 14px;
            resize: vertical;
            box-sizing: border-box;
            outline: none;
        }

        .notice-comment-textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        .notice-comment-submit-wrap {
            margin-top: 8px;
            display: flex;
            justify-content: flex-end;
        }

        .notice-comment-submit {
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .notice-comment-submit:hover {
            background: #1d4ed8;
        }
    </style>

    <div class="notice-feed">
        @forelse ($this->getFeedNotices() as $notice)
            <article class="notice-card">
                <header class="notice-header">
                    {{ $notice->title }}
                </header>

                @if (filled($notice->cover_image_path))
                    <div class="notice-cover">
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($notice->cover_image_path) }}"
                            alt="{{ $notice->title }}"
                        >
                    </div>
                @endif

                <section class="notice-meta-card">
                    <div class="notice-author">
                        <div class="notice-avatar">
                            @if (filled($notice->creator?->profile_photo_path))
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($notice->creator->profile_photo_path) }}"
                                    alt="{{ $notice->creator?->name }}"
                                >
                            @else
                                {{ \Illuminate\Support\Str::of($notice->creator?->name ?? 'MC')->explode(' ')->take(2)->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))->implode('') }}
                            @endif
                        </div>
                        <div>
                            <p class="notice-author-name">
                                {{ $notice->creator?->name ?? 'Movimento Casa' }} -> Movimento Casa
                            </p>
                            <p class="notice-author-date">
                                {{ $notice->published_at?->format('d/m/Y H:i') ?? $notice->created_at?->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="notice-content-card">
                    <p class="notice-content">{{ $notice->content }}</p>
                </section>

                <section class="notice-meta-card">
                    <div class="notice-stats">
                        <span class="notice-stats-like">&#128591; {{ $notice->likes_count }} curtidas</span>
                        <span>{{ $notice->comments_count }} comentarios</span>
                    </div>
                </section>

                <section class="notice-actions-card">
                    <div class="notice-actions-grid">
                        <button
                            type="button"
                            wire:click="toggleLike({{ $notice->getKey() }})"
                            class="notice-action-btn"
                        >
                            &#128591; {{ $notice->likes->isNotEmpty() ? 'Descurtir' : 'Curtir' }}
                        </button>
                        <button
                            type="button"
                            wire:click="toggleCommentForm({{ $notice->getKey() }})"
                            class="notice-action-btn"
                        >
                            &#128172; {{ ($commentFormVisible[$notice->getKey()] ?? false) ? 'Fechar' : 'Comentar' }}
                        </button>
                    </div>
                </section>

                <section class="notice-comments-card">
                    <h4 class="notice-comments-title">Comentarios</h4>

                    @forelse ($notice->comments as $comment)
                        <article class="notice-comment-item">
                            <div class="notice-comment-head">
                                <p class="notice-comment-author">{{ $comment->member?->full_name ?? 'Membro' }}</p>
                                <p class="notice-comment-date">{{ $comment->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <p class="notice-comment-text">{{ $comment->content }}</p>
                        </article>
                    @empty
                        <p class="notice-empty-comments">Ainda nao ha comentarios neste aviso.</p>
                    @endforelse
                </section>

                <section class="notice-comment-form-card" style="{{ ($commentFormVisible[$notice->getKey()] ?? false) ? '' : 'display:none;' }}">
                    <label class="notice-comment-label">Adicionar comentario</label>
                    <textarea
                        wire:model.defer="commentInputs.{{ $notice->getKey() }}"
                        class="notice-comment-textarea"
                        placeholder="Escreva seu comentario..."
                    ></textarea>
                    <div class="notice-comment-submit-wrap">
                        <button
                            type="button"
                            wire:click="submitComment({{ $notice->getKey() }})"
                            class="notice-comment-submit"
                        >
                            Publicar comentario
                        </button>
                    </div>
                </section>
            </article>
        @empty
            <div class="notice-card" style="text-align: center; color: #6b7280; font-size: 14px;">
                Nenhum aviso publicado no momento.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
