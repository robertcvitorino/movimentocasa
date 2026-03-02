@php
    /** @var \App\Models\FormationLesson $lesson */
    $supportDocuments = $lesson->support_documents;
@endphp

@if (count($supportDocuments))
    <div class="grid grid-cols-1 gap-6 pb-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($supportDocuments as $documentPath)
            @php($documentUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($documentPath))

            <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">

                <a
                    href="{{ $documentUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="absolute top-4 right-4 z-10 inline-flex items-center justify-center rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-600"
                >
                    Abrir PDF
                </a>

                <iframe
                    src="{{ $documentUrl }}#toolbar=0&navpanes=0&scrollbar=1"
                    class="block h-[600px] w-full bg-white"
                    title="Preview do documento de apoio"
                ></iframe>

            </div>
        @endforeach
    </div>
@else
    <div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
        Nenhum documento de apoio foi anexado.
    </div>
@endif
