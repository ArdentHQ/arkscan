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

        @php ($favoriteDelegates = $delegates->filter(fn ($slot) => $slot->isFavorite()))
        @if ($favoriteDelegates->isNotEmpty())
            <div class="pb-3 font-semibold md:hidden text-theme-secondary-700 dark:text-theme-dark-200">
                @lang('tables.delegate-monitor.my_favorites')
            </div>

            <x-tables.mobile.delegates.monitor :delegates="$favoriteDelegates" />

            <x-general.mobile-divider
                class="my-6 -mx-6"
                color="bg-theme-secondary-200 dark:bg-theme-dark-700 text-theme-secondary-200 dark:text-theme-dark-700"
            />
        @endif

        <x-tables.mobile.delegates.monitor :delegates="$delegates->filter(fn ($slot) => ! $slot->isFavorite())" />
    </x-skeletons.delegates.monitor>
</div>
