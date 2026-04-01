<?php

namespace App\Filament\Member\Resources\Notices\Tables;

use App\Models\Notice;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NoticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Aviso')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('content')
                    ->label('Conteudo')
                    ->wrap()
                    ->limit(220),
                TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->sinceTooltip(),
                TextColumn::make('likes_count')
                    ->label('Curtidas')
                    ->badge(),
                TextColumn::make('comments_count')
                    ->label('Comentarios')
                    ->badge(),
            ])
            ->recordActions([
                Action::make('toggleLike')
                    ->label(function (Notice $record): string {
                        $member = auth()->user()?->member;

                        if (! $member) {
                            return 'Curtir';
                        }

                        return $record->isLikedBy($member) ? 'Descurtir' : 'Curtir';
                    })
                    ->icon('heroicon-o-hand-thumb-up')
                    ->color(function (Notice $record): string {
                        $member = auth()->user()?->member;

                        if (! $member) {
                            return 'gray';
                        }

                        return $record->isLikedBy($member) ? 'success' : 'gray';
                    })
                    ->action(function (Notice $record): void {
                        $member = auth()->user()?->member;

                        if (! $member) {
                            return;
                        }

                        $existingLike = $record->likes()
                            ->where('member_id', $member->getKey())
                            ->first();

                        if ($existingLike) {
                            $existingLike->delete();

                            return;
                        }

                        $record->likes()->create([
                            'member_id' => $member->getKey(),
                        ]);
                    }),
                Action::make('comment')
                    ->label('Comentar')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->form([
                        Textarea::make('content')
                            ->label('Comentario')
                            ->required()
                            ->maxLength(2000)
                            ->rows(4),
                    ])
                    ->action(function (Notice $record, array $data): void {
                        $member = auth()->user()?->member;

                        if (! $member) {
                            Notification::make()
                                ->title('Membro nao encontrado.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->comments()->create([
                            'member_id' => $member->getKey(),
                            'content' => (string) $data['content'],
                        ]);

                        Notification::make()
                            ->title('Comentario publicado.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('published_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}

