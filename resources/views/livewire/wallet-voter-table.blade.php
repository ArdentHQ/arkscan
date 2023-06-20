<div class="w-full">
    <x-tables.toolbars.toolbar :result-count="$wallets->total()" />

    <x-skeletons.wallet-voters>
        <x-tables.desktop.wallet-voters
            :wallets="$wallets"
            :no-results-message="$this->noResultsMessage"
            without-truncate
        />

        <x-tables.mobile.wallet-voters
            :wallets="$wallets"
            :no-results-message="$this->noResultsMessage"
        />

        <x-general.pagination.table
            :results="$wallets"
            class="mt-4 md:mt-0"
        />

        <x-script.onload-scroll-to-query selector="#voters-list" />
    </x-skeletons.wallet-voters>
</div>
