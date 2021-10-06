<div id="wallet-list" class="w-full">
    <x-skeletons.wallets>
        <x-tables.desktop.wallets :wallets="$wallets" />

        <x-tables.mobile.wallets :wallets="$wallets" />

        <x-general.pagination :results="$wallets" class="mt-8" />

        <x-script.onload-scroll-to-query selector="#wallet-list" />
    </x-skeletons.wallets>
</div>
