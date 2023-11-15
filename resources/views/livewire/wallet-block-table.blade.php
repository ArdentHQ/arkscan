<div class="w-full">
    <x-tables.toolbars.blocks
        :blocks="$blocks"
        :wallet="$wallet"
    />

    <x-skeletons.wallet-blocks
        :row-count="$this->perPage"
        :paginator="$blocks"
    >
        <x-tables.desktop.wallet-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
            without-truncate
        />

        <x-tables.mobile.wallet-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.wallet-blocks>

    <x-general.pagination.table :results="$blocks" />
</div>
