<div id="delegate-list" class="w-full">
    <x-delegates.table-desktop :delegates="$delegates" />

    <x-delegates.list-mobile :delegates="$delegates" />

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
