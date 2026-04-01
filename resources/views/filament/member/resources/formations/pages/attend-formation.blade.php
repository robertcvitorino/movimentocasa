<x-filament-panels::page>
    <style>
        .formation-attend-wizard .fi-sc-wizard-header-step-icon-ctn {
            position: relative;
        }

        .formation-attend-wizard .fi-sc-wizard-header-step-number {
            position: absolute;
            inset: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .formation-attend-wizard .fi-sc-wizard-header-step.fi-completed .fi-sc-wizard-header-step-number {
            display: none;
        }
    </style>

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
