<div
    wire:init="monitorIsReady"
    @if ($this->isReady && $this->hasValidators)
        wire:poll.2s="pollData"
    @elseif ($this->isReady)
        wire:poll.4s="pollData"
    @endif
>
    <div class="px-6 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
        <x-validators.monitor.data-boxes
            :statistics="$statistics"
            :height="$height"
        />
    </div>

    <x-general.mobile-divider />

    <div class="px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl">
        <div
            id="validator-monitor-list"
            class="w-full"
        >
            <x-skeletons.validators.monitor>
                <x-tables.desktop.validators.monitor
                    :validators="$validators"
                    :round="$round"
                    :overflow-validators="$overflowValidators"
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
                        @lang('tables.validator-monitor.my_favorites')
                    </div>

                    <x-tables.mobile.validators.monitor
                        :validators="$validators"
                        :round="$round"
                    />
                </div>
            </x-skeletons.validators.monitor>
        </div>
    </div>
</div>
