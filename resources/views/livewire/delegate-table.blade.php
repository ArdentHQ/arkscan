<div id="delegate-list" class="w-full">
    <span x-show="status !== 'active'">
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
    </span>

    @if($this->state['status'] === 'active')
        <div wire:poll.{{ Network::blockTime() }}s wire:key="poll_active_delegates_skeleton">
            @if (count($delegates) && $state['status'] === 'active')
                <x-tables.desktop.delegates.active :delegates="$delegates" />
                <x-tables.mobile.delegates.active :delegates="$delegates" />
            @else
                <x-loading.hidden>
                    <x-tables.desktop.delegates.active :delegates="$delegates" />
                    <x-tables.mobile.delegates.active :delegates="$delegates" />
                </x-loading.hidden>
            @endif
        </div>
    @elseif (! count($delegates) || $state['status'] !== 'active')
        <span x-show="status === 'active'">
            <x-tables.desktop.skeleton.delegates.active />
            <x-tables.mobile.skeleton.delegates.active />
        </span>
    @endif

    @if($this->state['status'] === 'standby')
        <x-loading.hidden>
            <x-tables.desktop.delegates.standby :delegates="$delegates" />
            <x-tables.mobile.delegates.standby :delegates="$delegates" />

            <x-general.pagination :results="$delegates" class="mt-8" />
        </x-loading.hidden>
    @endif

    @if($this->state['status'] === 'resigned')
        <x-loading.hidden>
            <x-tables.desktop.delegates.resigned :delegates="$delegates" />
            <x-tables.mobile.delegates.resigned :delegates="$delegates" />

            <x-general.pagination :results="$delegates" class="mt-8" />
        </x-loading.hidden>
    @endif

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
