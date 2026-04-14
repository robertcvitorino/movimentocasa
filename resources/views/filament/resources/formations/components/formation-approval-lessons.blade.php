@php
    /** @var \App\Models\Formation $formation */
    $lessons = $formation->activeLessons->sortBy('display_order')->values();
@endphp

@if($lessons->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma aula ativa cadastrada.</p>
@else
    <ul class="divide-y divide-gray-100 dark:divide-white/5 -my-3">
        @foreach($lessons as $index => $lesson)
        <li class="flex items-center gap-3 py-3">
            <span class="fi-badge fi-color-custom fi-size-sm flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                  style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                {{ $index + 1 }}
            </span>
            <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $lesson->title }}</span>
            @if($lesson->estimated_duration_minutes)
                <span class="fi-badge fi-color-gray fi-size-sm ms-auto">
                    {{ $lesson->estimated_duration_minutes }} min
                </span>
            @endif
        </li>
        @endforeach
    </ul>
@endif
