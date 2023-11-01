<div
    id="delegate-monitor-list"
    class="w-full"
    wire:init="monitorIsReady"
    @if ($this->isReady && $this->hasDelegates)
        wire:poll.1s="pollDelegates"
    @elseif ($this->isReady)
        wire:poll.8s="pollDelegates"
    @endif
>
    <x-skeletons.delegates.monitor>
        <x-tables.desktop.delegates.monitor :delegates="$delegates" />

        <div
            x-data="{
                toggleFavorite(address, isFavorite) {
                    if (isFavorite) {
                        $root.querySelector(`[x-ref='favorite-delegate-${address}']`).classList.remove('hidden');
                        $root.querySelector(`[x-ref='delegate-${address}']`).classList.add('hidden');
                    } else {
                        $root.querySelector(`[x-ref='favorite-delegate-${address}']`).classList.add('hidden');
                        $root.querySelector(`[x-ref='delegate-${address}']`).classList.remove('hidden');
                    }
                },
            }"
            class="md:hidden"
        >
            @php ($favoriteDelegates = $delegates->filter(fn ($slot) => $slot->isFavorite()))
            @if ($favoriteDelegates->isNotEmpty())
                <div class="pb-3 font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('tables.delegate-monitor.my_favorites')
                </div>

                <x-tables.mobile.delegates.monitor
                    :delegates="$delegates"
                    favorites
                />

                <x-general.mobile-divider
                    class="my-6 -mx-6"
                    color="bg-theme-secondary-200 dark:bg-theme-dark-700 text-theme-secondary-200 dark:text-theme-dark-700"
                />
            @endif

            <x-tables.mobile.delegates.monitor :delegates="$delegates" />
        </div>
    </x-skeletons.delegates.monitor>
</div>
