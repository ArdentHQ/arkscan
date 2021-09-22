<div>
    @if(! count($delegates))
        <div wire:poll="pollDelegates" wire:key="poll_delegates_skeleton">
            <x-tables.desktop.skeleton.delegates.monitor />
        </div>
    @else
        <div id="network-list" class="w-full" wire:poll.{{ Network::blockTime() }}s="pollDelegates" wire:key="poll_delegates_real">
            <x-tables.desktop.delegates.monitor :delegates="$delegates" :round="$round" />
        </div>
    @endif
</div>
