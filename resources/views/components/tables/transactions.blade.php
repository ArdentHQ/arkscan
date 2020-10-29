<div id="transaction-list" class="w-full">
    <div wire:loading>
        <x-transactions.table-desktop-skeleton />

        <x-transactions.table-mobile-skeleton />
    </div>

    <div wire:loading.remove>
        <x-transactions.table-desktop :transactions="$transactions" />

        <x-transactions.table-mobile :transactions="$transactions" />

        <x-general.pagination :results="$transactions" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
        </script>
    </div>
</div>
