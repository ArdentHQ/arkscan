<div class="w-full">
    <x-tables.toolbars.delegates.missed-blocks :blocks="$blocks" />

    <x-skeletons.delegates.missed-blocks :row-count="$this->perPage">
        <x-tables.desktop.delegates.missed-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.delegates.missed-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.delegates.missed-blocks>

    <x-general.pagination.table
        :results="$blocks"
        class="mt-4 md:mt-0"
    />
</div>
