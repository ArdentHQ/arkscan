<div id="block-list" class="w-full">
    <x-skeletons.wallets>
        <x-wallets.table-desktop :wallets="$wallets" without-truncate />

        <x-wallets.table-mobile :wallets="$wallets" />

        <x-general.pagination :results="$wallets" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
        </script>
    </x-skeletons.wallets>
</div>
