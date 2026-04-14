<?php

namespace App\Filament\Member\Resources\Formations\Pages;

use App\Actions\Formation\CompleteFormationLessonAction;
use App\Actions\Formation\EnsureFormationProgressAction;
use App\Actions\Formation\IssueCertificateAction;
use App\Actions\Formation\SubmitQuizAttemptAction;
use App\Filament\Member\Resources\Formations\FormationResource;
use App\Models\FormationLesson;
use App\Models\MemberFormationProgress;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class AttendFormation extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = FormationResource::class;

    protected string $view = 'filament.member.resources.formations.pages.attend-formation';

    protected Width | string | null $maxContentWidth = Width::Full;

    public ?array $data = [];

    public ?MemberFormationProgress $progress = null;

    /** Quiz já enviado (sessão atual ou anterior) */
    public bool $quizSubmitted = false;

    /** Nota da última tentativa */
    public ?float $lastAttemptScore = null;

    /** Total de tentativas realizadas */
    public int $attemptCount = 0;

    /** Limite de tentativas atingido */
    public bool $attemptLimitReached = false;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->loadProgress();
        $this->syncAttemptState();
        $this->form->fill(['quiz_answers' => []]);
    }

    public function getTitle(): string
    {
        return $this->getRecord()->title;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Resumo da formação')
                    ->schema([
                        Text::make(fn (): string => 'Ministério: ' . ($this->getRecord()->ministry?->name ?: '-'))
                            ->color('primary'),
                        Text::make(fn (): string => 'Progresso: ' . ($this->progress?->progress_percentage ?? 0) . '%')
                            ->color('secondary'),
                        Text::make(fn (): string => 'Status: ' . ($this->progress?->status?->label() ?? 'Não iniciada'))
                            ->color('danger'),
                    ])
                    ->columns(3)
                    ->extraAttributes(['class' => 'py-2'])
                    ->columnSpanFull(),

                Wizard::make($this->getSteps())
                    ->persistStepInQueryString()
                    ->startOnStep($this->getStartStep())
                    ->extraAttributes(['class' => 'formation-attend-wizard'])
                    ->nextAction(fn (Action $action): Action => $action->extraAttributes([
                        'x-on:click' => 'window.pauseFormationMedia?.()',
                    ]))
                    ->previousAction(fn (Action $action): Action => $action->extraAttributes([
                        'x-on:click' => 'window.pauseFormationMedia?.()',
                    ]))
                    ->submitAction($this->getWizardSubmitAction())
                    ->contained(false)
                    ->skippable(false)
                    ->extraAlpineAttributes([
                        'x-on:next-wizard-step.window' => 'window.pauseFormationMedia?.()',
                        'x-on:go-to-wizard-step.window' => 'window.pauseFormationMedia?.()',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    // -------------------------------------------------------------------------
    // Steps
    // -------------------------------------------------------------------------

    /** @return array<Step> */
    protected function getSteps(): array
    {
        $steps = [];

        // Aulas
        foreach ($this->getLessons()->values() as $index => $lesson) {
            $stepNumber = $index + 1;

            $steps[] = Step::make('Aula ' . $stepNumber)
                ->id('lesson-' . $lesson->getKey())
                ->key('lesson-' . $lesson->getKey())
                ->description($lesson->title)
                ->columnSpanFull()
                ->schema($this->getLessonStepSchema($lesson))
                ->afterValidation(fn () => $this->completeLessonStep($lesson));
        }

        // Quiz (opcional)
        if ($this->hasActiveQuiz()) {
            $steps[] = Step::make('Quiz')
                ->id('final-quiz')
                ->key('final-quiz')
                ->description('Responda ao quiz para acompanhar seu desempenho.')
                ->schema($this->getQuizSchema())
                ->afterValidation(fn () => $this->handleQuizStep());
        }

        // Certificado (sempre presente)
        $steps[] = Step::make('Certificado')
            ->id('certificate')
            ->key('certificate')
            ->description('Conclusão da formação.')
            ->schema($this->getCertificateStepSchema());

        return $steps;
    }

    /** @return array<\Filament\Schemas\Components\Component> */
    protected function getLessonStepSchema(FormationLesson $lesson): array
    {
        return [
            View::make('filament.member.resources.formations.components.lesson-player')
                ->viewData([
                    'lesson' => $lesson,
                    'embedUrl' => $this->getLessonEmbedUrl($lesson),
                ])
                ->columnSpanFull(),

            Section::make($lesson->title)
                ->description('Assista ao vídeo e leia o conteúdo de apoio para concluir esta etapa.')
                ->schema([
                    View::make('filament.member.resources.formations.components.lesson-support-content')
                        ->viewData([
                            'formation' => $this->getRecord(),
                            'lesson' => $lesson,
                        ]),
                ])
                ->extraAttributes(['class' => 'py-2'])
                ->columnSpanFull(),

            Section::make('Documentos de apoio')
                ->description('Visualização somente leitura dos anexos desta etapa.')
                ->schema([
                    View::make('filament.member.resources.formations.components.lesson-support-documents')
                        ->viewData(['lesson' => $lesson]),
                ])
                ->extraAttributes(['class' => 'py-2'])
                ->columnSpanFull(),
        ];
    }

    /** @return array<\Filament\Forms\Components\Field|\Filament\Schemas\Components\Component> */
    protected function getQuizSchema(): array
    {
        $quiz = $this->getRecord()->quiz;

        if (! $quiz) {
            return [Text::make('Não há quiz configurado para esta formação.')->color('warning')];
        }

        // Quiz já enviado (sessão atual ou anterior): mostra resultado
        if ($this->quizSubmitted) {
            return [
                Text::make(
                    '✅ Quiz já enviado. Sua nota: ' . number_format((float) ($this->lastAttemptScore ?? 0), 2, ',', '.') . '%'
                )->color('success'),
                Text::make('Clique em "Próximo" para prosseguir ao certificado.')->color('secondary'),
            ];
        }

        // Limite atingido: mostra aviso
        if ($this->attemptLimitReached) {
            $components = [
                Text::make(
                    '⚠️ Você atingiu o limite de ' . $quiz->max_attempts . ' tentativa(s).'
                )->color('warning'),
            ];

            if ($this->lastAttemptScore !== null) {
                $components[] = Text::make(
                    'Sua última nota: ' . number_format($this->lastAttemptScore, 2, ',', '.') . '%'
                )->color('secondary');
            }

            $components[] = Text::make('Clique em "Próximo" para prosseguir ao certificado.')->color('secondary');

            return $components;
        }

        // Exibe as perguntas
        $questions = $quiz->questions
            ->where('is_active', true)
            ->sortBy('display_order');

        if ($questions->isEmpty()) {
            return [Text::make('Não há perguntas disponíveis neste quiz.')->color('warning')];
        }

        return $questions
            ->map(fn ($question) => Radio::make('quiz_answers.' . $question->getKey())
                ->label($question->display_order . '. ' . $question->statement)
                ->options(
                    $question->options
                        ->sortBy('display_order')
                        ->mapWithKeys(fn ($option) => [$option->getKey() => $option->label])
                        ->all()
                )
                ->required()
                ->columns(1)
                ->columnSpanFull()
            )
            ->values()
            ->all();
    }

    /** @return array<\Filament\Schemas\Components\Component> */
    protected function getCertificateStepSchema(): array
    {
        $components = [];

        if ($this->lastAttemptScore !== null) {
            $label = $this->quizSubmitted
                ? 'Sua nota no quiz: '
                : 'Sua última nota no quiz: ';

            $components[] = Text::make(
                $label . number_format($this->lastAttemptScore, 2, ',', '.') . '%'
            )->color('secondary');
        }

        $components[] = Text::make('🎓 Formação concluída! Clique em "Gerar certificado" para receber seu certificado.')
            ->color('success');

        return $components;
    }

    // -------------------------------------------------------------------------
    // Handlers
    // -------------------------------------------------------------------------

    /**
     * Chamado via afterValidation no step do Quiz.
     * Submete as respostas e avança para o step de certificado.
     */
    public function handleQuizStep(): void
    {
        // Nada a fazer se já enviado ou no limite
        if ($this->quizSubmitted || $this->attemptLimitReached) {
            return;
        }

        try {
            $attempt = app(SubmitQuizAttemptAction::class)->execute(
                $this->progress,
                $this->data['quiz_answers'] ?? [],
            );

            $this->lastAttemptScore = (float) $attempt->score;
            $this->quizSubmitted = true;
            $this->attemptCount++;

            $this->loadProgress();

            Notification::make()
                ->title('Quiz enviado!')
                ->body('Sua nota: ' . number_format($this->lastAttemptScore, 2, ',', '.') . '%')
                ->success()
                ->send();

        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            if (isset($errors['limit_reached'])) {
                $this->attemptLimitReached = true;
                $this->loadProgress();

                $lastAttempt = $this->progress?->quizAttempts()
                    ->latest('submitted_at')
                    ->first();

                $this->lastAttemptScore = $lastAttempt ? (float) $lastAttempt->score : null;

                Notification::make()
                    ->title('Limite de tentativas atingido')
                    ->body(collect($errors)->except('limit_reached')->flatten()->implode(' '))
                    ->warning()
                    ->send();

                return; // Permite avançar ao certificado mesmo assim
            }

            Notification::make()
                ->title('Não foi possível enviar o quiz')
                ->body(collect($errors)->flatten()->implode(' '))
                ->danger()
                ->send();

            throw $exception;
        }
    }

    /** Chamado pelo botão "Gerar certificado" (submit do wizard, último step). */
    public function submit(): void
    {
        if (! $this->progress) {
            abort(403);
        }

        $this->loadProgress();

        if (
            $this->progress->status === \App\Enums\FormationProgressStatus::Completed
            && $this->getRecord()->certificate_enabled
        ) {
            app(IssueCertificateAction::class)->execute($this->progress);
        }

        Notification::make()
            ->title('Formação concluída!')
            ->success()
            ->send();

        $this->redirect(FormationResource::getUrl(), navigate: true);
    }

    // -------------------------------------------------------------------------
    // Progress / State
    // -------------------------------------------------------------------------

    public function loadProgress(): void
    {
        $member = auth()->user()?->member;
        abort_if(! $member, 403);

        $this->progress = app(EnsureFormationProgressAction::class)->execute(
            $member,
            $this->getRecord()->load([
                'lessons' => fn ($query) => $query->where('is_active', true),
                'ministry',
                'quiz.questions.options',
                'progress.lessonProgress',
            ])
        );
    }

    /**
     * Sincroniza o estado do quiz com o progresso persistido
     * (para usuários que retornam após uma sessão anterior).
     */
    protected function syncAttemptState(): void
    {
        if (! $this->hasActiveQuiz() || ! $this->progress) {
            return;
        }

        $quiz = $this->getRecord()->quiz;
        $this->attemptCount = $this->progress->quizAttempts()->count();

        // Quiz já foi enviado em sessão anterior
        if ($this->progress->quiz_passed_at !== null) {
            $this->quizSubmitted = true;
            $lastAttempt = $this->progress->quizAttempts()->latest('submitted_at')->first();
            $this->lastAttemptScore = $lastAttempt ? (float) $lastAttempt->score : null;

            return;
        }

        // Limite de tentativas atingido
        if ($quiz && $quiz->max_attempts > 0 && $this->attemptCount >= $quiz->max_attempts) {
            $this->attemptLimitReached = true;
            $lastAttempt = $this->progress->quizAttempts()->latest('submitted_at')->first();
            $this->lastAttemptScore = $lastAttempt ? (float) $lastAttempt->score : null;
        }
    }

    public function getStartStep(): int
    {
        $lessonCount = $this->getLessons()->count();

        $completedLessons = $this->progress?->lessonProgress
            ?->where('status', \App\Enums\LessonProgressStatus::Completed)
            ->count() ?? 0;

        // Ainda tem aulas pendentes
        if ($completedLessons < $lessonCount) {
            return $completedLessons + 1;
        }

        // Todas as aulas concluídas — tem quiz e ainda não foi enviado/limite
        if ($this->hasActiveQuiz() && ! $this->quizSubmitted && ! $this->attemptLimitReached) {
            return $lessonCount + 1; // step do quiz
        }

        // Vai direto ao certificado (último step)
        return $lessonCount + ($this->hasActiveQuiz() ? 2 : 1);
    }

    protected function completeLessonStep(FormationLesson $lesson): void
    {
        if (! $this->progress) {
            return;
        }

        $alreadyCompleted = $this->progress->lessonProgress
            ->where('formation_lesson_id', $lesson->getKey())
            ->where('status', \App\Enums\LessonProgressStatus::Completed)
            ->isNotEmpty();

        if ($alreadyCompleted) {
            return;
        }

        app(CompleteFormationLessonAction::class)->execute($this->progress, $lesson);
        $this->loadProgress();

        Notification::make()
            ->title('Aula concluída')
            ->success()
            ->send();
    }

    public function getLessons(): Collection
    {
        return $this->getRecord()->lessons
            ->where('is_active', true)
            ->sortBy('display_order')
            ->values();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function hasActiveQuiz(): bool
    {
        $record = $this->getRecord();

        if (! $record->quiz_enabled) {
            return false;
        }

        $quiz = $record->quiz;

        if (! $quiz || ! $quiz->is_active) {
            return false;
        }

        return $quiz->questions->where('is_active', true)->isNotEmpty();
    }

    protected function getWizardSubmitAction(): Htmlable
    {
        return new HtmlString(Blade::render(<<<'BLADE'
            <x-filament::button type="submit" size="lg" x-on:click="window.pauseFormationMedia?.()">
                Gerar certificado
            </x-filament::button>
        BLADE));
    }

    protected function getLessonEmbedUrl(FormationLesson $lesson): ?string
    {
        if (! $lesson->video_url) {
            return null;
        }

        $videoId = $this->extractYoutubeVideoId($lesson->video_url);

        return $videoId
            ? sprintf('https://www.youtube-nocookie.com/embed/%s?rel=0&modestbranding=1&enablejsapi=1', $videoId)
            : $lesson->video_url;
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
