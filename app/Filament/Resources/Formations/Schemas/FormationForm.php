<?php

namespace App\Filament\Resources\Formations\Schemas;

use App\Enums\FormationStatus;
use App\Enums\LessonSourceType;
use App\Enums\QuestionType;
use App\Models\Ministry;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class FormationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados gerais')
                    ->columnSpanFull()
                    ->columns(6)
                    ->schema([
                        FileUpload::make('cover_image_path')
                            ->label('Capa')
                            ->image()
                            ->disk('public')
                            ->directory('formations/covers')
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Titulo')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),

                        Select::make('ministry_id')
                            ->label('Ministerio')
                            ->columnSpan(2)
                            ->options(fn (): array => Ministry::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                $slug = Ministry::query()->whereKey($state)->value('slug');

                                if (filled($slug)) {
                                    $set('slug', $slug);
                                }
                            })
                            ->required(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->columnSpan(1)
                            ->unique(ignoreRecord: true),

                        TextInput::make('workload_hours')
                            ->label('Carga horaria')
                            ->numeric(),

                        TextInput::make('minimum_score')
                            ->label('Nota minima')
                            ->numeric()
                            ->default(70),

                        Select::make('status')
                            ->label('Status')
                            ->options(collect(FormationStatus::cases())->mapWithKeys(fn (FormationStatus $status) => [$status->value => $status->label()]))
                            ->required(),

                        Toggle::make('is_required')
                            ->label('Obrigatorio')
                            ->inline(),

                        Toggle::make('certificate_enabled')
                            ->inline()
                            ->label('Gera certificado?'),

                        RichEditor::make('full_description')
                            ->label('Descricao')
                            ->extraInputAttributes([
                                'style' => 'min-height: 6rem;',
                            ])
                            ->columnSpanFull(),


                    ]),
                Section::make('Videoaulas')
                    ->columns(6)
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('lessons')
                            ->relationship()
                            ->orderColumn('display_order')
                            ->columnSpanFull()
                            ->columns(6)
                            ->label('Aulas')
                            ->defaultItems(0)
                            ->collapsed()
                            ->reorderableWithButtons()
                            ->afterStateHydrated(function (?array $state, Set $set): void {
                                $normalizedState = self::normalizeRepeaterDisplayOrder($state ?? []);

                                if (($state ?? []) !== $normalizedState) {
                                    $set('lessons', $normalizedState);
                                }
                            })
                            ->afterStateUpdated(function (?array $state, Set $set): void {
                                $normalizedState = self::normalizeRepeaterDisplayOrder($state ?? []);

                                if (($state ?? []) !== $normalizedState) {
                                    $set('lessons', $normalizedState);
                                }
                            })
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Aula')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titulo da aula')
                                    ->columnSpan(3)
                                    ->required(),

                                Select::make('source_type')
                                    ->label('Origem')
                                    ->options(collect(LessonSourceType::cases())->mapWithKeys(fn (LessonSourceType $type) => [$type->value => $type->label()]))
                                    ->required()
                                    ->live(),

                                TextInput::make('display_order')
                                    ->label('Ordem')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->required(),

                                TextInput::make('estimated_duration_minutes')
                                    ->label('Duracao (min)')
                                    ->numeric(),

                                TextInput::make('video_url')
                                    ->label('Link do video')
                                    ->url()
                                    ->visible(fn (Get $get): bool => $get('source_type') === LessonSourceType::Youtube->value)
                                    ->required(fn (Get $get): bool => $get('source_type') === LessonSourceType::Youtube->value)
                                    ->columnSpanFull(),

                                FileUpload::make('video_path')
                                    ->label('Arquivo da aula')
                                    ->disk('public')
                                    ->directory('formations/videos')
                                    ->visible(fn (Get $get): bool => $get('source_type') === LessonSourceType::Upload->value)
                                    ->required(fn (Get $get): bool => $get('source_type') === LessonSourceType::Upload->value)
                                    ->columnSpanFull(),

                                Toggle::make('is_required')
                                    ->label('Obrigatoria')
                                    ->default(true),

                                Toggle::make('is_active')
                                    ->label('Ativa')
                                    ->default(true),

                                Textarea::make('description')
                                    ->label('Descricao')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                RichEditor::make('support_text')
                                    ->label('Texto de apoio da etapa')
                                    ->columnSpanFull()
                                    ->extraInputAttributes([
                                        'style' => 'min-height: 6rem;',
                                    ]),

                                AdvancedFileUpload::make('support_document_paths')
                                    ->label('Documentos de apoio')
                                    ->disk('public')
                                    ->directory('formations/support-materials')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->multiple()
                                    ->reorderable()
                                    ->downloadable()
                                    ->maxFiles(5)
                                    ->maxSize(10240) // 10MB
                                    ->panelLayout('grid')
                                    ->pdfDisplayPage(1)
                                    ->pdfFitType(PdfViewFit::FITBH)
                                    ->pdfPreviewHeight(320)
                                    ->pdfZoomLevel(100)
                                    ->pdfNavPanes(false)
                                    ->openable()
                                    ->multiple()
                                    ->maxSize(2048)
                                    ->uploadingMessage('Carregando...')
                                    ->previewable(true)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Prova final')
                    ->relationship('quiz')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Titulo da prova')
                            ->default('Prova final'),
                        TextInput::make('minimum_score')
                            ->label('Nota minima')
                            ->numeric()
                            ->default(70),
                        TextInput::make('max_attempts')
                            ->label('Tentativas maximas')
                            ->numeric()
                            ->default(3),
                        Toggle::make('is_active')
                            ->label('Quiz ativo')
                            ->default(true),
                        Repeater::make('questions')
                            ->relationship()
                            ->label('Perguntas')
                            ->defaultItems(0)
                            ->collapsed()
                            ->reorderableWithButtons()
                            ->itemLabel(fn (array $state): ?string => $state['statement'] ?? 'Pergunta')
                            ->schema([
                                Textarea::make('statement')
                                    ->label('Enunciado')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Select::make('question_type')
                                    ->label('Tipo')
                                    ->options(collect(QuestionType::cases())->mapWithKeys(fn (QuestionType $type) => [$type->value => $type->label()]))
                                    ->default(QuestionType::MultipleChoice->value)
                                    ->required(),
                                TextInput::make('weight')
                                    ->label('Peso')
                                    ->numeric()
                                    ->default(1),
                                TextInput::make('display_order')
                                    ->label('Ordem')
                                    ->numeric()
                                    ->default(1),
                                Toggle::make('is_active')
                                    ->label('Ativa')
                                    ->default(true),
                                Repeater::make('options')
                                    ->relationship()
                                    ->label('Alternativas')
                                    ->minItems(2)
                                    ->defaultItems(2)
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Texto')
                                            ->required(),
                                        Toggle::make('is_correct')
                                            ->label('Correta'),
                                        TextInput::make('display_order')
                                            ->label('Ordem')
                                            ->numeric()
                                            ->default(1),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                  ,
            ]);
    }

    public static function normalizeLessonsFormData(array $data): array
    {
        if (is_array($data['lessons'] ?? null)) {
            $data['lessons'] = self::normalizeRepeaterDisplayOrder($data['lessons']);
        }

        return $data;
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $items
     * @return array<int|string, array<string, mixed>>
     */
    protected static function normalizeRepeaterDisplayOrder(array $items): array
    {
        $displayOrder = 1;

        foreach ($items as $key => $item) {
            if (! is_array($item)) {
                continue;
            }

            $item['display_order'] = $displayOrder++;
            $items[$key] = $item;
        }

        return $items;
    }
}
