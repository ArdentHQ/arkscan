<div id="delegate-list" class="w-full">
    <x-loading.visible>
        @if($this->state['status'] === 'active')
            <x-tables.desktop.skeleton.monitor.active />
            <x-tables.mobile.skeleton.monitor.active />
        @endif

        @if($this->state['status'] === 'standby')
            <x-tables.desktop.skeleton.monitor.standby />
            <x-tables.mobile.skeleton.monitor.standby />
        @endif

        @if($this->state['status'] === 'resigned')
            <x-tables.desktop.skeleton.monitor.resigned />
            <x-tables.mobile.skeleton.monitor.resigned />
        @endif
    </x-loading.visible>

    @if($this->state['status'] === 'active')
        <x-loading.hidden>
            <x-tables.desktop.monitor.active :delegates="$delegates" />
            <x-tables.mobile.monitor.active :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'standby')
        <x-loading.hidden>
            <x-tables.desktop.monitor.standby :delegates="$delegates" />
            <x-tables.mobile.monitor.standby :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'resigned')
        <x-loading.hidden>
            <x-tables.desktop.monitor.resigned :delegates="$delegates" />
            <x-tables.mobile.monitor.resigned :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
