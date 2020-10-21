<div id="wallet-list" class="w-full">
    <x-wallets.table-desktop :wallets="$wallets" with-rank />
    <x-wallets.list-mobile :wallets="$wallets" with-rank />

    <x-general.pagination :results="$wallets" class="mt-8" />

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#wallet-list')));
    </script>
</div>
