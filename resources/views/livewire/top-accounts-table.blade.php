<div
    id="wallet-list"
    class="w-full"
    wire:init="setIsReady"
>
    <x-skeletons.top-accounts
        :row-count="$this->perPage"
        :paginator="$wallets"
    >
        <x-tables.desktop.top-accounts :wallets="$wallets" />

        <x-tables.mobile.top-accounts :wallets="$wallets" />
    </x-skeletons.top-accounts>

    <x-general.pagination.table :results="$wallets" />

    <x-script.onload-scroll-to-query selector="#wallet-list" />
</div>
