<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-10">
        {{ $this->form }}

        <x-filament-actions::modals />
    </form>

    <script>
        window.pauseFormationMedia = function () {
            document.querySelectorAll('video').forEach((video) => {
                try {
                    video.pause()
                } catch (error) {
                }
            })

            document.querySelectorAll('iframe[data-formation-youtube-player="true"]').forEach((iframe) => {
                try {
                    iframe.contentWindow?.postMessage(
                        JSON.stringify({
                            event: 'command',
                            func: 'pauseVideo',
                            args: [],
                        }),
                        '*',
                    )
                } catch (error) {
                }
            })
        }
    </script>
</x-filament-panels::page>
