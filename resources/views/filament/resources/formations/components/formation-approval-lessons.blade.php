@php
    /** @var \App\Models\Formation $formation */
    $lessons = $formation->activeLessons->sortBy('display_order')->values();
@endphp

@if($lessons->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma aula ativa cadastrada.</p>
@else
    <ul class="divide-y divide-gray-100 dark:divide-white/5">
        @foreach($lessons as $index => $lesson)
        <li class="flex items-center gap-4 py-4">
            <span class="fi-badge fi-color-custom fi-size-md flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                  style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                {{ $index + 1 }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-950 dark:text-white">{{ $lesson->title }}</p>
                @if($lesson->source_type)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $lesson->source_type->label() }}</p>
                @endif
            </div>
            @if($lesson->estimated_duration_minutes)
                <span class="fi-badge fi-color-gray fi-size-md shrink-0">
                    {{ $lesson->estimated_duration_minutes }} min
                </span>
            @endif
        </li>
        @endforeach
    </ul>
@endif
