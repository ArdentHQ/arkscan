<div id="block-list" class="w-full">
    <x-skeletons.wallets>
        <x-tables.desktop.wallets :wallets="$wallets" without-truncate />

        <x-tables.mobile.wallets :wallets="$wallets" />

        <x-general.pagination :results="$wallets" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
        </script>
    </x-skeletons.wallets>
</div>
