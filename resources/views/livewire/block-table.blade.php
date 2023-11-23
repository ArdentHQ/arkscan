<div
    id="blocks-list"
    class="w-full"
    wire:init="setIsReady"
>
    <x-tables.toolbars.toolbar :result-count="$blocks->total()" />

    <x-skeletons.blocks
        :row-count="$this->perPage"
        :paginator="$blocks"
    >
        <x-tables.desktop.blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.blocks>

    <x-general.pagination.table :results="$blocks" />

    <x-script.onload-scroll-to-query selector="#blocks-list" />
</div>
