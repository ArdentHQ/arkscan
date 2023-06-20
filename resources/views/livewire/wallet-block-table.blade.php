<div class="w-full">
    <x-tables.toolbars.blocks :blocks="$blocks" />

    <x-skeletons.wallet-blocks>
        <x-tables.desktop.wallet-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
            without-truncate
        />

        <x-tables.mobile.wallet-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />

        <x-general.pagination.table
            :results="$blocks"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.wallet-blocks>
</div>
