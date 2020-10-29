<div id="delegate-list" class="w-full">
    @if($this->state['status'] === 'resigned')
        <div class="w-full" wire:loading>
            <x-delegates.table-desktop-resigned-skeleton />
            <x-delegates.table-mobile-resigned-skeleton />
        </div>

        <div class="w-full" wire:loading.remove>
            <x-delegates.table-desktop-resigned :delegates="$delegates" />
            <x-delegates.table-mobile-resigned :delegates="$delegates" />
        </div>
    @endif

    @if($this->state['status'] === 'standby')
        <div class="w-full" wire:loading>
            <x-delegates.table-desktop-standby-skeleton />
            <x-delegates.table-mobile-standby-skeleton />
        </div>

        <div class="w-full" wire:loading.remove>
            <x-delegates.table-desktop-standby :delegates="$delegates" />
            <x-delegates.table-mobile-standby :delegates="$delegates" />
        </div>
    @endif

    @if($this->state['status'] === 'active')
        <div class="w-full" wire:loading>
            <x-delegates.table-desktop-active-skeleton />
            <x-delegates.table-mobile-active-skeleton />
        </div>

        <div class="w-full" wire:loading.remove>
            <x-delegates.table-desktop-active :delegates="$delegates" />
            <x-delegates.table-mobile-active :delegates="$delegates" />
        </div>
    @endif

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
    </script>
</div>
