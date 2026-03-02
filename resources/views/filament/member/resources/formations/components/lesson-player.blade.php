<div class="-mx-4 overflow-hidden rounded-2xl border border-gray-200 bg-black shadow-sm sm:-mx-6 lg:-mx-8">
    @if ($lesson->source_type->value === 'youtube' && $embedUrl)
        <div class="relative w-full bg-black" style="height: clamp(28rem, 78vh, 60rem);">
            <iframe
                class="absolute inset-0 block h-full w-full"
                style="width: 100%; height: 100%;"
                src="{{ $embedUrl }}"
                title="{{ $lesson->title }}"
                data-formation-youtube-player="true"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
            ></iframe>
        </div>
    @elseif ($lesson->video_path)
        <div class="relative w-full bg-black" style="height: clamp(28rem, 78vh, 60rem);">
            <video class="absolute inset-0 block h-full w-full bg-black" style="width: 100%; height: 100%;" controls preload="metadata">
                <source src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($lesson->video_path) }}">
                Seu navegador nao suporta reproducao de video.
            </video>
        </div>
    @else
        <div class="flex w-full items-center justify-center px-6 text-center text-sm text-gray-300" style="height: clamp(28rem, 78vh, 60rem);">
            O video desta aula ainda nao foi configurado.
        </div>
    @endif
</div>
