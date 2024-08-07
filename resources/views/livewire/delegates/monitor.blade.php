<div
    id="delegate-monitor-list"
    class="w-full"
    wire:init="monitorIsReady"
    @if (config('broadcasting.default') !== 'reverb')
        @if ($this->isReady && $this->hasDelegates)
            wire:poll.1s="pollDelegates"
        @elseif ($this->isReady)
            wire:poll.8s="pollDelegates"
        @endif
    @endif
>
    <x-skeletons.delegates.monitor>
        <x-tables.desktop.delegates.monitor
            :delegates="$delegates"
            :round="$round"
        />

        <div
            class="md:hidden"
            x-data="{
                hasFavorites: false,
                toggleFavorites() {
                    $nextTick(() => {
                        this.hasFavorites = $root.querySelectorAll(`[data-favorite='1']`).length > 0;
                    });
                },
            }"
            x-init="toggleFavorites"
        >
            <div
                x-show="hasFavorites"
                class="pb-3 font-semibold text-theme-secondary-700 dark:text-theme-dark-200"
            >
                @lang('tables.delegate-monitor.my_favorites')
            </div>

            <x-tables.mobile.delegates.monitor
                :delegates="$delegates"
                :round="$round"
            />
        </div>
    </x-skeletons.delegates.monitor>
</div>
