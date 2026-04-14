<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Enums\FormationApprovalStatus;
use App\Enums\FormationStatus;
use App\Filament\Resources\Formations\FormationApprovalResource;
use App\Models\FormationLesson;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;

class ReviewFormationApproval extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = FormationApprovalResource::class;

    protected string $view = 'filament.resources.formations.pages.review-formation-approval';

    protected Width | string | null $maxContentWidth = Width::Full;

    public ?array $data = [];

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->record->load([
            'ministry',
            'creator',
            'activeLessons',
            'quiz.questions.options',
        ]);

        $this->form->fill([
            'decision'       => null,
            'approval_notes' => null,
        ]);
    }

    public function getTitle(): string
    {
        return 'Revisar: ' . $this->getRecord()->title;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Tabs')
                    ->tabs($this->getTabs())
                    ->persistTab()
                    ->contained(false)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<Tab>
     */
    protected function getTabs(): array
    {
        $tabs = [];

        $lessons = $this->getRecord()->activeLessons
            ->sortBy('display_order')
            ->values();

        foreach ($lessons as $index => $lesson) {
            $tabs[] = Tab::make('Aula ' . ($index + 1))
                ->id('lesson-' . $lesson->getKey())
                ->icon('heroicon-o-play-circle')
                ->schema($this->getLessonStepSchema($lesson));
        }

        $tabs[] = Tab::make('Avaliação')
            ->id('review-decision')
            ->icon('heroicon-o-clipboard-document-check')
            ->schema($this->getDecisionStepSchema());

        return $tabs;
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    protected function getLessonStepSchema(FormationLesson $lesson): array
    {
        return [
            View::make('filament.member.resources.formations.components.lesson-player')
                ->viewData([
                    'lesson'   => $lesson,
                    'embedUrl' => $this->getLessonEmbedUrl($lesson),
                ])
                ->columnSpanFull(),

            Section::make($lesson->title)
                ->description('Conteúdo de apoio da aula.')
                ->schema([
                    View::make('filament.member.resources.formations.components.lesson-support-content')
                        ->viewData([
                            'formation' => $this->getRecord(),
                            'lesson'    => $lesson,
                        ]),
                ])
                ->extraAttributes(['class' => 'py-2'])
                ->columnSpanFull(),

            Section::make('Documentos de apoio')
                ->description('Anexos disponíveis para esta aula.')
                ->schema([
                    View::make('filament.member.resources.formations.components.lesson-support-documents')
                        ->viewData(['lesson' => $lesson]),
                ])
                ->extraAttributes(['class' => 'py-2'])
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    protected function getDecisionStepSchema(): array
    {
        $formation = $this->getRecord();

        return [
            // ── Dados da Formação ─────────────────────────────────
            Section::make('Dados da Formação')
                ->description('Informações gerais sobre a formação submetida para revisão.')
                ->columns(4)
                ->schema([
                    Placeholder::make('title')
                        ->label('Título')
                        ->content(fn (): string => $formation->title)
                        ->columnSpan(2),

                    Placeholder::make('ministry')
                        ->label('Ministério')
                        ->content(fn (): string => $formation->ministry?->name ?? '—'),

                    Placeholder::make('workload_hours')
                        ->label('Carga horária')
                        ->content(fn (): string => $formation->workload_hours
                            ? number_format($formation->workload_hours, 0) . 'h'
                            : '—'),

                    Placeholder::make('creator')
                        ->label('Criado por')
                        ->content(fn (): string => $formation->creator?->name ?? '—'),

                    Placeholder::make('submitted_for_review_at')
                        ->label('Enviado para revisão')
                        ->content(fn (): string => $formation->submitted_for_review_at?->format('d/m/Y \à\s H:i') ?? '—'),

                    Placeholder::make('configuration')
                        ->label('Configurações')
                        ->content(fn (): HtmlString => $this->getConfigurationBadges($formation))
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            // ── Descrição ─────────────────────────────────────────
            Section::make('Descrição')
                ->columns(1)
                ->visible(fn (): bool => filled($formation->short_description) || filled($formation->full_description))
                ->schema([
                    Placeholder::make('short_description')
                        ->label('Resumo')
                        ->content(fn (): string => $formation->short_description ?? '')
                        ->visible(fn (): bool => filled($formation->short_description)),

                    Placeholder::make('full_description')
                        ->label('Descrição completa')
                        ->content(fn (): HtmlString => new HtmlString($formation->full_description ?? ''))
                        ->visible(fn (): bool => filled($formation->full_description)),
                ])
                ->columnSpanFull(),

            // ── Aulas ─────────────────────────────────────────────
            Section::make('Aulas')
                ->description(fn (): string => $formation->activeLessons->count() . ' aula(s) ativa(s)')
                ->schema([
                    View::make('filament.resources.formations.components.formation-approval-lessons')
                        ->viewData(['formation' => $formation])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            // ── Quiz ──────────────────────────────────────────────
            Section::make('Quiz — ' . ($formation->quiz?->title ?? ''))
                ->description(fn (): string => ($formation->quiz?->questions->count() ?? 0) . ' pergunta(s) · ' . ($formation->quiz?->max_attempts ?? 0) . ' tentativa(s)')
                ->visible(fn (): bool => (bool) $formation->quiz_enabled && $formation->quiz !== null)
                ->schema([
                    Placeholder::make('quiz_info')
                        ->hiddenLabel()
                        ->content('Revise as questões do quiz nas abas de cada aula.'),
                ])
                ->columnSpanFull(),

            // ── Decisão ───────────────────────────────────────────
            Section::make('Decisão de Revisão')
                ->description('Após revisar todo o conteúdo, registre sua decisão abaixo.')
                ->schema([
                    Radio::make('decision')
                        ->label('Qual é a sua decisão?')
                        ->options([
                            'approve' => 'Aprovar formação — publicar imediatamente para os membros',
                            'refine'  => 'Solicitar refinamento — devolver ao criador com observações',
                        ])
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    Textarea::make('approval_notes')
                        ->label('Observações para o criador')
                        ->placeholder('Descreva os ajustes necessários...')
                        ->visible(fn (Get $get): bool => $get('decision') === 'refine')
                        ->required(fn (Get $get): bool => $get('decision') === 'refine')
                        ->rows(5)
                        ->columnSpanFull(),

                    View::make('filament.resources.formations.components.formation-approval-submit')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }

    private function getConfigurationBadges(\App\Models\Formation $formation): HtmlString
    {
        $badges = [];

        $badges[] = $formation->is_required
            ? '<span class="fi-badge fi-color-custom fi-size-md" style="--c-400:var(--warning-400);--c-600:var(--warning-600);">Obrigatória</span>'
            : '<span class="fi-badge fi-color-gray fi-size-md">Não obrigatória</span>';

        $badges[] = $formation->certificate_enabled
            ? '<span class="fi-badge fi-color-custom fi-size-md" style="--c-400:var(--success-400);--c-600:var(--success-600);">Gera certificado</span>'
            : '<span class="fi-badge fi-color-gray fi-size-md">Sem certificado</span>';

        $badges[] = $formation->quiz_enabled
            ? '<span class="fi-badge fi-color-custom fi-size-md" style="--c-400:var(--info-400);--c-600:var(--info-600);">Tem quiz</span>'
            : '<span class="fi-badge fi-color-gray fi-size-md">Sem quiz</span>';

        if ($formation->is_general ?? false) {
            $badges[] = '<span class="fi-badge fi-color-custom fi-size-md" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">Formação geral</span>';
        }

        return new HtmlString('<div class="flex flex-wrap gap-2">' . implode('', $badges) . '</div>');
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        if ($state['decision'] === 'approve') {
            $this->record->update([
                'status'          => FormationStatus::Published,
                'approval_status' => FormationApprovalStatus::Approved,
                'reviewed_by'     => auth()->id(),
                'reviewed_at'     => now(),
                'published_at'    => now(),
            ]);

            Notification::make()
                ->title('Formação aprovada e publicada!')
                ->success()
                ->send();
        } else {
            $this->record->update([
                'approval_status' => FormationApprovalStatus::NeedsRefinement,
                'approval_notes'  => $state['approval_notes'],
                'reviewed_by'     => auth()->id(),
                'reviewed_at'     => now(),
            ]);

            Notification::make()
                ->title('Refinamento solicitado ao criador.')
                ->warning()
                ->send();
        }

        $this->redirect(FormationApprovalResource::getUrl('index'));
    }

    protected function getLessonEmbedUrl(FormationLesson $lesson): ?string
    {
        if (! $lesson->video_url) {
            return null;
        }

        $videoId = $this->extractYoutubeVideoId($lesson->video_url);

        if (! $videoId) {
            return $lesson->video_url;
        }

        return sprintf('https://www.youtube-nocookie.com/embed/%s?rel=0&modestbranding=1&enablejsapi=1', $videoId);
    }

    protected function extractYoutubeVideoId(string $url): ?string
    {
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return null;
        }

        $host = $parts['host'] ?? '';
        $path = trim($parts['path'] ?? '', '/');

        if (str_contains($host, 'youtu.be')) {
            return $path !== '' ? explode('/', $path)[0] : null;
        }

        if (! str_contains($host, 'youtube.com')) {
            return null;
        }

        if ($path === 'watch') {
            parse_str($parts['query'] ?? '', $query);

            return $query['v'] ?? null;
        }

        if (str_starts_with($path, 'embed/')) {
            return explode('/', $path)[1] ?? null;
        }

        if (str_starts_with($path, 'shorts/')) {
            return explode('/', $path)[1] ?? null;
        }

        return null;
    }
}
