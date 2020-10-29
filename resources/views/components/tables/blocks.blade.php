<div id="block-list" class="w-full">
    <div class="w-full" wire:loading>
        <x-blocks.table-desktop-skeleton />

        <x-blocks.table-mobile-skeleton />
    </div>

    <div class="w-full" wire:loading.remove>
        <x-blocks.table-desktop :blocks="$blocks" />

        <x-blocks.table-mobile :blocks="$blocks" />

        <x-general.pagination :results="$blocks" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
        </script>
    </div>
</div>
