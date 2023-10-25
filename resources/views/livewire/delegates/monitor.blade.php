{{-- <div>
    @if(! count($delegates))
        <div wire:poll="pollDelegates" wire:key="poll_delegates_skeleton">
            <x-tables.desktop.skeleton.delegates.monitor />
        </div>
    @else
        <div id="network-list" class="w-full" wire:poll.{{ Network::blockTime() }}s="pollDelegates" wire:key="poll_delegates_real">
            <x-tables.desktop.delegates.monitor :delegates="$delegates" :round="$round" />
        </div>
    @endif
</div> --}}

<div
    id="delegate-monitor-list"
    class="w-full"
    wire:init="monitorIsReady"
    @if ($this->isReady)
        wire:poll.1s="pollDelegates"
    @endif
>
    <x-skeletons.delegates.monitor>
        <x-tables.desktop.delegates.monitor />

        <x-tables.mobile.delegates.monitor />
    </x-skeletons.delegates.monitor>
</div>
