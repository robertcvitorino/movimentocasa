<?php

namespace App\Filament\Member\Resources\Formations\Pages;

use App\Actions\Formation\CompleteFormationLessonAction;
use App\Actions\Formation\EnsureFormationProgressAction;
use App\Actions\Formation\SubmitQuizAttemptAction;
use App\Filament\Member\Resources\Formations\FormationResource;
use App\Models\Formation;
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
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
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

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->loadProgress();
        $this->form->fill([
            'quiz_answers' => [],
        ]);
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
                Section::make('Resumo da formacao')
                    ->schema([
                        Text::make(fn (): string => 'Ministerio: ' . ($this->getRecord()->ministry?->name ?: '-'))->color('primary'),
                        Text::make(fn (): string => 'Progresso: ' . ($this->progress?->progress_percentage ?? 0) . '%')->color('secondary'),
                        Text::make(fn (): string => 'Status: ' . ($this->progress?->status?->label() ?? 'Nao iniciada'))->color('danger'),
                    ])
                    ->extraAttributes([
                        'class' => 'py-2',
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Wizard::make($this->getSteps())
                    ->persistStepInQueryString()
                    ->startOnStep($this->getStartStep())
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

    /**
     * @return array<Step>
     */
    protected function getSteps(): array
    {
        $steps = [];

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

        if ($this->getRecord()->quiz?->is_active) {
            $steps[] = Step::make('Prova final')
                ->id('final-quiz')
                ->key('final-quiz')
                ->description('Responda a avaliacao para concluir a formacao.')
                ->schema($this->getQuizSchema());
        }

        return $steps;
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
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
                ->description('Assista ao video e leia o conteudo de apoio para concluir esta etapa.')
                ->schema([
                    View::make('filament.member.resources.formations.components.lesson-support-content')
                        ->viewData([
                            'formation' => $this->getRecord(),
                            'lesson' => $lesson,
                        ]),
                ])
                ->extraAttributes([
                    'class' => 'py-2',
                ])
                ->columnSpanFull(),
            Section::make('Documentos de apoio')
                ->description('Visualização somente leitura dos anexos desta etapa.')
                ->schema([

                    View::make('filament.member.resources.formations.components.lesson-support-documents')
                        ->viewData([
                            'lesson' => $lesson,
                        ]),
                ])
                ->extraAttributes([
                    'class' => 'py-2',
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<\Filament\Forms\Components\Field>
     */
    protected function getQuizSchema(): array
    {
        $quiz = $this->getRecord()->quiz;

        if (! $quiz) {
            return [
                Radio::make('quiz_unavailable')
                    ->label('Nao ha prova configurada para esta formacao.')
                    ->disabled(),
            ];
        }

        return $quiz->questions
            ->where('is_active', true)
            ->sortBy('display_order')
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
                ->columnSpanFull())
            ->values()
            ->all();
    }

    public function getStartStep(): int
    {
        $completedLessons = $this->progress?->lessonProgress
            ?->where('status', \App\Enums\LessonProgressStatus::Completed)
            ->count() ?? 0;

        $lessonCount = $this->getLessons()->count();

        if ($completedLessons < $lessonCount) {
            return $completedLessons + 1;
        }

        if (! $this->getRecord()->quiz?->is_active) {
            return max($lessonCount, 1);
        }

        return $lessonCount + 1;
    }

    public function submit(): void
    {
        $quiz = $this->getRecord()->quiz;

        if (! $quiz || ! $this->progress) {
            Notification::make()
                ->title('Esta formacao nao possui prova configurada')
                ->warning()
                ->send();

            return;
        }

        $state = $this->form->getState();

        try {
            $attempt = app(SubmitQuizAttemptAction::class)->execute(
                $this->progress,
                $state['quiz_answers'] ?? [],
            );
        } catch (ValidationException $exception) {
            Notification::make()
                ->title('Nao foi possivel enviar a prova')
                ->body(collect($exception->errors())->flatten()->implode(' '))
                ->danger()
                ->send();

            throw $exception;
        }

        $this->loadProgress();

        Notification::make()
            ->title($attempt->status->value === 'passed' ? 'Prova aprovada' : 'Prova enviada')
            ->body(
                $attempt->status->value === 'passed'
                    ? 'Nota final: ' . $attempt->score . '%.'
                        . ($this->getRecord()->certificate_enabled ? ' Certificado emitido com sucesso.' : '')
                    : 'Nota final: ' . $attempt->score . '%.'
            )
            ->success()
            ->send();

        $this->redirect(FormationResource::getUrl(), navigate: true);
    }

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

    public function getLessons(): Collection
    {
        return $this->getRecord()->lessons
            ->where('is_active', true)
            ->sortBy('display_order')
            ->values();
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
            ->title('Aula concluida')
            ->success()
            ->send();
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

    protected function getWizardSubmitAction(): Htmlable
    {
        return new HtmlString(Blade::render(<<<'BLADE'
            <x-filament::button type="submit" size="lg" x-on:click="window.pauseFormationMedia?.()">
                Enviar prova final
            </x-filament::button>
        BLADE));
    }
}
