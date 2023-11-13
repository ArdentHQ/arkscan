<div class="w-full">
    <x-tables.toolbars.toolbar :result-count="$wallets->total()" />

    <x-skeletons.wallet-voters
        :row-count="$this->perPage"
        :paginator="$wallets"
    >
        <x-tables.desktop.wallet-voters
            :wallets="$wallets"
            :no-results-message="$this->noResultsMessage"
            without-truncate
        />

        <x-tables.mobile.wallet-voters
            :wallets="$wallets"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.wallet-voters>

    <x-general.pagination.table :results="$wallets" />
</div>
