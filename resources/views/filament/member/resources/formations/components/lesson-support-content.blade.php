@php
    /** @var \App\Models\Formation $formation */
    /** @var \App\Models\FormationLesson $lesson */
@endphp

<div class="space-y-4">
{{--    @if ($formation->full_description)--}}
{{--        <div class="prose max-w-none rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-white/5 dark:prose-invert">--}}
{{--            {!! $formation->full_description !!}--}}
{{--        </div>--}}
{{--    @endif--}}

    @if ($lesson->support_text)
        <div class="prose max-w-none rounded-xl border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-white/5 dark:prose-invert">
            {!! $lesson->support_text !!}
        </div>
    @endif

    @if (blank($formation->full_description) && blank($lesson->support_text))
        <div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
            Nenhum conteudo de apoio.
        </div>
    @endif
</div>
