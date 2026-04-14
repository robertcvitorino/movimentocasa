@php
    use Illuminate\Support\Facades\Storage;
    use App\Enums\LessonSourceType;

    /** @var \App\Models\Formation $formation */
    $formation = $getRecord();
    $lessons   = $formation->activeLessons;
    $quiz      = $formation->quiz;

    /**
     * Extrai o ID de um vídeo YouTube a partir de diversas variações de URL.
     */
    $getYoutubeId = function (?string $url): ?string {
        if (blank($url)) return null;
        preg_match(
            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|shorts\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
            $url,
            $matches
        );
        return $matches[1] ?? null;
    };
@endphp

<div class="space-y-10">

    {{-- ── Descrição ──────────────────────────────────────────────── --}}
    @if(filled($formation->short_description) || filled($formation->full_description))
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 overflow-hidden">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-white/10 px-6 py-4">
            <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">Descrição</h3>
        </div>
        <div class="px-6 py-5 space-y-4">
            @if(filled($formation->short_description))
            <div>
                <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Resumo</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $formation->short_description }}</p>
            </div>
            @endif
            @if(filled($formation->full_description))
            <div>
                <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Descrição completa</p>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! $formation->full_description !!}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Aulas ────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 overflow-hidden">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-white/10 px-6 py-4">
            <x-filament::icon icon="heroicon-o-play-circle" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                Aulas
                <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $lessons->count() }})</span>
            </h3>
        </div>

        @if($lessons->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                Nenhuma aula cadastrada.
            </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-white/5">
            @foreach($lessons as $index => $lesson)
            @php
                $youtubeId   = $lesson->source_type === LessonSourceType::Youtube ? $getYoutubeId($lesson->video_url) : null;
                $videoUrl    = $lesson->source_type === LessonSourceType::Upload && filled($lesson->video_path)
                                    ? Storage::disk('public')->url($lesson->video_path)
                                    : null;
                $documents   = array_values(array_filter($lesson->support_document_paths ?? []));
            @endphp

            <div class="px-6 py-6 space-y-5">

                {{-- ── Cabeçalho da aula ─────────────────────────── --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-base font-semibold text-gray-950 dark:text-white leading-tight">
                            {{ $lesson->title }}
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center justify-end gap-2">
                        @if($lesson->estimated_duration_minutes)
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-white/10 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-clock" class="h-3.5 w-3.5" />
                            {{ $lesson->estimated_duration_minutes }} min
                        </span>
                        @endif
                        @if($lesson->is_required)
                        <span class="inline-flex items-center gap-1 rounded-md bg-warning-100 px-2 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-950 dark:text-warning-400">
                            <x-filament::icon icon="heroicon-o-exclamation-circle" class="h-3.5 w-3.5" />
                            Obrigatória
                        </span>
                        @endif
                        @if($lesson->source_type)
                        <span class="inline-flex items-center rounded-md bg-info-100 px-2 py-0.5 text-xs font-medium text-info-700 dark:bg-info-950 dark:text-info-400">
                            {{ $lesson->source_type->label() }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- ── Player YouTube ─────────────────────────────── --}}
                @if($youtubeId)
                <div class="overflow-hidden rounded-xl bg-black aspect-video shadow-md">
                    <iframe
                        src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?rel=0&modestbranding=1"
                        class="h-full w-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                        title="{{ $lesson->title }}"
                    ></iframe>
                </div>
                @elseif(!$youtubeId && filled($lesson->video_url))
                {{-- Link YouTube sem ID detectável --}}
                <div class="flex items-center gap-2 rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-3">
                    <x-filament::icon icon="heroicon-o-link" class="h-4 w-4 shrink-0 text-gray-400" />
                    <a href="{{ $lesson->video_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="text-sm text-primary-600 hover:underline dark:text-primary-400 truncate">
                        {{ $lesson->video_url }}
                    </a>
                </div>
                @endif

                {{-- ── Player de vídeo Upload ─────────────────────── --}}
                @if($videoUrl)
                <div class="overflow-hidden rounded-xl shadow-md">
                    <video
                        controls
                        preload="metadata"
                        class="w-full rounded-xl bg-black"
                        style="max-height: 480px;"
                    >
                        <source src="{{ $videoUrl }}" type="video/mp4">
                        <p class="p-4 text-sm text-gray-500">
                            Seu navegador não suporta reprodução de vídeo.
                            <a href="{{ $videoUrl }}" class="text-primary-600 underline">Baixar vídeo</a>
                        </p>
                    </video>
                </div>
                @endif

                {{-- ── Texto de apoio ──────────────────────────────── --}}
                @if(filled($lesson->support_text))
                <div>
                    <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Conteúdo de apoio
                    </p>
                    <div class="rounded-xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-5 py-4 prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! $lesson->support_text !!}
                    </div>
                </div>
                @endif

                {{-- ── Documentos de apoio ─────────────────────────── --}}
                @if(!empty($documents))
                <div>
                    <p class="mb-3 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Documentos de apoio ({{ count($documents) }})
                    </p>
                    <div class="space-y-4">
                        @foreach($documents as $docPath)
                        @php
                            $docUrl  = Storage::disk('public')->url($docPath);
                            $docName = basename($docPath);
                        @endphp
                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 shadow-sm">
                            {{-- Cabeçalho do documento --}}
                            <div class="flex items-center justify-between gap-3 bg-gray-50 dark:bg-white/5 px-4 py-2.5 border-b border-gray-200 dark:border-white/10">
                                <div class="flex items-center gap-2 min-w-0">
                                    <x-filament::icon icon="heroicon-o-document-text" class="h-4 w-4 shrink-0 text-danger-500" />
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                        {{ $docName }}
                                    </span>
                                </div>
                                <a href="{{ $docUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-primary-600 hover:bg-primary-700 px-3 py-1 text-xs font-medium text-white transition-colors">
                                    <x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-3.5 w-3.5" />
                                    Baixar
                                </a>
                            </div>
                            {{-- Preview PDF incorporado --}}
                            <iframe
                                src="{{ $docUrl }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH"
                                class="w-full"
                                style="height: 520px;"
                                frameborder="0"
                                loading="lazy"
                                title="{{ $docName }}"
                            ></iframe>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Quiz ──────────────────────────────────────────────────────── --}}
    @if($formation->quiz_enabled && $quiz)
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 overflow-hidden">
        <div class="flex items-center justify-between gap-2 border-b border-gray-200 dark:border-white/10 px-6 py-4">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    Quiz — {{ $quiz->title }}
                </h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $quiz->questions->count() }} pergunta(s)
                </span>
                <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-white/10 dark:text-gray-400">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="h-3.5 w-3.5" />
                    {{ $quiz->max_attempts }} tentativa(s)
                </span>
                @if($quiz->is_active)
                <span class="inline-flex items-center gap-1 rounded-md bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-950 dark:text-success-400">
                    Ativo
                </span>
                @else
                <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-white/10 dark:text-gray-400">
                    Inativo
                </span>
                @endif
            </div>
        </div>

        @if($quiz->questions->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                Nenhuma pergunta cadastrada.
            </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-white/5">
            @foreach($quiz->questions->sortBy('display_order') as $qIndex => $question)
            <div class="px-6 py-5 space-y-3">
                {{-- Enunciado --}}
                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-info-100 text-xs font-bold text-info-700 dark:bg-info-950 dark:text-info-300">
                        {{ $qIndex + 1 }}
                    </span>
                    <p class="pt-0.5 text-sm font-medium text-gray-950 dark:text-white leading-relaxed">
                        {{ $question->statement }}
                    </p>
                </div>
                {{-- Alternativas --}}
                @if($question->options->isNotEmpty())
                <div class="ml-10 grid gap-2 sm:grid-cols-2">
                    @foreach($question->options->sortBy('display_order') as $option)
                    <div @class([
                        'flex items-center gap-2.5 rounded-lg border px-3.5 py-2.5 text-sm transition-colors',
                        'border-success-300 bg-success-50 dark:border-success-700 dark:bg-success-950/50' => $option->is_correct,
                        'border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5' => !$option->is_correct,
                    ])>
                        @if($option->is_correct)
                            <x-filament::icon icon="heroicon-o-check-circle" class="h-4 w-4 shrink-0 text-success-600 dark:text-success-400" />
                            <span class="font-semibold text-success-700 dark:text-success-300">{{ $option->label }}</span>
                        @else
                            <x-filament::icon icon="heroicon-o-x-circle" class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" />
                            <span class="text-gray-600 dark:text-gray-400">{{ $option->label }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

</div>
