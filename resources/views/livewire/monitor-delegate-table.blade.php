<div id="delegate-list" class="w-full">
    @if($this->state['status'] === 'resigned')
        <x-delegates.table-desktop-resigned :delegates="$delegates" />
        <x-delegates.list-mobile-resigned :delegates="$delegates" />
    @endif

    @if($this->state['status'] === 'standby')
        <x-delegates.table-desktop-standby :delegates="$delegates" />
        <x-delegates.list-mobile-standby :delegates="$delegates" />
    @endif

    @if($this->state['status'] === 'active')
        <x-delegates.table-desktop-active :delegates="$delegates" />
        <x-delegates.list-mobile-active :delegates="$delegates" />
    @endif

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
