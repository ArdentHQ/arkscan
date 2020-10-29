<div id="transaction-list" class="w-full">
    <x-skeletons.transactions>
        <x-transactions.table-desktop :transactions="$transactions" />

        <x-transactions.table-mobile :transactions="$transactions" />

        <x-general.pagination :results="$transactions" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
        </script>
    </x-skeletons.transactions>
</div>
