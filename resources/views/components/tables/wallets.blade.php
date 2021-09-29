<div id="wallet-list" class="w-full">
    <x-skeletons.wallets>
        <x-tables.desktop.wallets :wallets="$wallets" />

        <x-tables.mobile.wallets :wallets="$wallets" />

        <x-general.pagination :results="$wallets" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#wallet-list')));
        </script>
    </x-skeletons.wallets>
</div>
