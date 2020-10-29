<div id="block-list" class="w-full">
    <x-skeletons.blocks>
        <x-blocks.table-desktop :blocks="$blocks" />

        <x-blocks.table-mobile :blocks="$blocks" />

        <x-general.pagination :results="$blocks" class="mt-8" />

        <script>
            window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
        </script>
    </x-skeletons.blocks>
</div>
