<div id="delegate-list" class="w-full">
    @if($this->state['status'] === 'resigned')
        <x-loading.visible>
            <x-delegates.table-desktop-resigned-skeleton />
            <x-delegates.table-mobile-resigned-skeleton />
        </x-loading.visible>

        <x-loading.hidden>
            <x-delegates.table-desktop-resigned :delegates="$delegates" />
            <x-delegates.table-mobile-resigned :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'standby')
        <x-loading.visible>
            <x-delegates.table-desktop-standby-skeleton />
            <x-delegates.table-mobile-standby-skeleton />
        </x-loading.visible>

        <x-loading.hidden>
            <x-delegates.table-desktop-standby :delegates="$delegates" />
            <x-delegates.table-mobile-standby :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'active')
        <x-loading.visible>
            <x-delegates.table-desktop-active-skeleton />
            <x-delegates.table-mobile-active-skeleton />
        </x-loading.visible>

        <x-loading.hidden>
            <x-delegates.table-desktop-active :delegates="$delegates" />
            <x-delegates.table-mobile-active :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
