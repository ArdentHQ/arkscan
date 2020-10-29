<div id="wallet-list" class="w-full">
    <div class="w-full" wire:loading>
        <x-wallets.table-desktop-skeleton />

        <x-wallets.table-mobile-skeleton />
    </div>

    <div class="w-full" wire:loading.remove>
        <x-wallets.table-desktop :wallets="$wallets" />

        <x-wallets.table-mobile :wallets="$wallets" />

        <x-general.pagination :results="$wallets" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
        </script>
    </div>
</div>
