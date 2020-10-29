<div id="transaction-list" class="w-full">
    <div wire:loading>
        <x-transactions.table-skeleton />
    </div>

    <div wire:loading.remove>
        <x-transactions.table-desktop :transactions="$transactions" />

        <x-transactions.list-mobile :transactions="$transactions" />

        <x-general.pagination :results="$transactions" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
        </script>
    </div>
</div>
