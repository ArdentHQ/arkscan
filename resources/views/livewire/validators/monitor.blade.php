<div
    id="validator-monitor-list"
    class="w-full"
    wire:init="monitorIsReady"
    @if ($this->isReady && $this->hasValidators)
        wire:poll.2s="pollValidators"
    @elseif ($this->isReady)
        wire:poll.4s="pollValidators"
    @endif
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
