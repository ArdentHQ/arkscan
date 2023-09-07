<div id="wallet-list" class="w-full">
    <x-skeletons.top-accounts :row-count="$this->perPage">
        <x-tables.desktop.top-accounts :wallets="$wallets" />

        <x-tables.mobile.top-accounts :wallets="$wallets" />
    </x-skeletons.top-accounts>

    <x-general.pagination.table :results="$wallets" />

    <x-script.onload-scroll-to-query selector="#wallet-list" />
</div>
