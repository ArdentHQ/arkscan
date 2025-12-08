<div
    wire:init="monitorIsReady"
    @if (config('broadcasting.default') !== 'reverb')
        @if ($this->isReady && $this->hasDelegates)
            wire:poll.1s="pollData"
        @elseif ($this->isReady)
            wire:poll.8s="pollData"
        @endif
    @endif
>
    <div class="px-6 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
        <x-delegates.monitor.data-boxes
            :statistics="$statistics"
            :height="$height"
        />
    </div>

    <x-general.mobile-divider />

    <div class="px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl">
        <div
            id="delegate-monitor-list"
            class="w-full"
        >
            <x-skeletons.delegates.monitor>
                <x-tables.desktop.delegates.monitor
                    :delegates="$delegates"
                    :round="$round"
                    :overflow-delegates="$overflowDelegates"
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
    </div>
</div>
