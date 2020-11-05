<div id="delegate-list" class="w-full">
    <x-loading.visible>
        <span x-show="status === 'active'">
            <x-tables.desktop.skeleton.delegates.active />
            <x-tables.mobile.skeleton.delegates.active />
        </span>

        <span x-show="status === 'standby'">
            <x-tables.desktop.skeleton.delegates.standby />
            <x-tables.mobile.skeleton.delegates.standby />
        </span>

        <span x-show="status === 'resigned'">
            <x-tables.desktop.skeleton.delegates.resigned />
            <x-tables.mobile.skeleton.delegates.resigned />
        </span>
    </x-loading.visible>

    @if($this->state['status'] === 'active')
        <x-loading.hidden>
            <x-tables.desktop.delegates.active :delegates="$delegates" />
            <x-tables.mobile.delegates.active :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'standby')
        <x-loading.hidden>
            <x-tables.desktop.delegates.standby :delegates="$delegates" />
            <x-tables.mobile.delegates.standby :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'resigned')
        <x-loading.hidden>
            <x-tables.desktop.delegates.resigned :delegates="$delegates" />
            <x-tables.mobile.delegates.resigned :delegates="$delegates" />
        </x-loading.hidden>
    @endif

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
