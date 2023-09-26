<div
    class="w-full"
    @if ($this->isReady)
        wire:poll.8s
    @endif
>
    <x-skeletons.home.blocks :row-count="$this->perPage">
        <x-tables.desktop.home.blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.home.blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.footers.view-all
            :results="$blocks"
            :count-suffix="trans('tables.home.blocks')"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.home.blocks>
</div>
