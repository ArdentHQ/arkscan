<div id="transaction-list" class="w-full">
    <x-skeletons.transactions>
        <x-tables.desktop.transactions :transactions="$transactions" />

        <x-tables.mobile.transactions :transactions="$transactions" />

        <x-general.pagination :results="$transactions" class="mt-8" />

        <x-script.onload-scroll-to-query selector="#transaction-list" />
    </x-skeletons.transactions>
</div>
