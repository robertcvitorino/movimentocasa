@php
    /** @var \App\Models\Formation $formation */
    $lessons = $formation->activeLessons->sortBy('display_order')->values();
    $quiz    = $formation->quiz;
@endphp

{{-- ── Dados da Formação ──────────────────────────────────────────── --}}
<x-filament::section>
    <x-slot name="heading">Dados da Formação</x-slot>
    <x-slot name="description">Informações gerais sobre a formação submetida para revisão.</x-slot>

    <div class="fi-sc-section-content-ctn">

        {{-- Título --}}
        <div class="mb-4">
            <p class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Título</p>
            <p class="text-xl font-bold text-gray-950 dark:text-white mt-1">{{ $formation->title }}</p>
        </div>

        {{-- Grid de campos --}}
        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-4">

            <div>
                <dt class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Ministério</dt>
                <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $formation->ministry?->name ?? '—' }}</dd>
            </div>

            <div>
                <dt class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Criado por</dt>
                <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $formation->creator?->name ?? '—' }}</dd>
            </div>

            <div>
                <dt class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Carga horária</dt>
                <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                    {{ $formation->workload_hours ? number_format($formation->workload_hours, 0) . 'h' : '—' }}
                </dd>
            </div>

            <div>
                <dt class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Enviado para revisão</dt>
                <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                    {{ $formation->submitted_for_review_at?->format('d/m/Y \à\s H:i') ?? '—' }}
                </dd>
            </div>

        </dl>

        {{-- Configurações --}}
        <div class="mt-5 flex flex-wrap gap-2">
            <x-filament::badge :color="$formation->is_required ? 'warning' : 'gray'" icon="{{ $formation->is_required ? 'heroicon-o-exclamation-circle' : 'heroicon-o-minus-circle' }}">
                {{ $formation->is_required ? 'Obrigatória' : 'Não obrigatória' }}
            </x-filament::badge>

            <x-filament::badge :color="$formation->certificate_enabled ? 'success' : 'gray'" icon="{{ $formation->certificate_enabled ? 'heroicon-o-academic-cap' : 'heroicon-o-minus-circle' }}">
                {{ $formation->certificate_enabled ? 'Gera certificado' : 'Sem certificado' }}
            </x-filament::badge>

            <x-filament::badge :color="$formation->quiz_enabled ? 'info' : 'gray'" icon="{{ $formation->quiz_enabled ? 'heroicon-o-clipboard-document-list' : 'heroicon-o-minus-circle' }}">
                {{ $formation->quiz_enabled ? 'Tem quiz' : 'Sem quiz' }}
            </x-filament::badge>

            @if($formation->is_general ?? false)
            <x-filament::badge color="primary" icon="heroicon-o-globe-alt">
                Formação geral
            </x-filament::badge>
            @endif
        </div>

    </div>
</x-filament::section>

{{-- ── Descrição ──────────────────────────────────────────────────── --}}
@if(filled($formation->short_description) || filled($formation->full_description))
<x-filament::section class="mt-4">
    <x-slot name="heading">Descrição</x-slot>

    <div class="space-y-4">
        @if(filled($formation->short_description))
        <div>
            <p class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Resumo</p>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $formation->short_description }}</p>
        </div>
        @endif
        @if(filled($formation->full_description))
        <div>
            <p class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">Descrição completa</p>
            <div class="prose prose-sm dark:prose-invert max-w-none mt-1">
                {!! $formation->full_description !!}
            </div>
        </div>
        @endif
    </div>
</x-filament::section>
@endif

{{-- ── Aulas ───────────────────────────────────────────────────────── --}}
<x-filament::section class="mt-4">
    <x-slot name="heading">Aulas</x-slot>
    <x-slot name="description">{{ $lessons->count() }} aula(s) ativa(s)</x-slot>

    <ul class="-mx-6 divide-y divide-gray-100 dark:divide-white/5">
        @forelse($lessons as $index => $lesson)
        <li class="flex items-center gap-3 px-6 py-3">
            <span class="fi-badge fi-color-primary flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold">
                {{ $index + 1 }}
            </span>
            <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $lesson->title }}</span>
            @if($lesson->estimated_duration_minutes)
            <span class="ml-auto flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                <x-filament::icon icon="heroicon-o-clock" class="h-3.5 w-3.5" />
                {{ $lesson->estimated_duration_minutes }} min
            </span>
            @endif
        </li>
        @empty
        <li class="px-6 py-6 text-sm text-gray-400 text-center">Nenhuma aula ativa.</li>
        @endforelse
    </ul>
</x-filament::section>

{{-- ── Quiz ────────────────────────────────────────────────────────── --}}
@if($formation->quiz_enabled && $quiz)
<x-filament::section class="mt-4">
    <x-slot name="heading">Quiz — {{ $quiz->title }}</x-slot>
    <x-slot name="description">{{ $quiz->questions->count() }} pergunta(s) · {{ $quiz->max_attempts }} tentativa(s)</x-slot>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        Revise as questões do quiz nas abas de cada aula.
    </p>
</x-filament::section>
@endif
