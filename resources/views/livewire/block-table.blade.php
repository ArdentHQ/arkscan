<div id="block-list" class="w-full" wire:poll.8s>
    <x-blocks.table-desktop :blocks="$blocks" />

    <x-blocks.list-mobile :blocks="$blocks" />

    <x-general.pagination :results="$blocks" class="mt-8" />

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
    </script>
</div>
