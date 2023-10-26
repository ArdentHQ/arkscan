<div
    id="delegate-monitor-list"
    class="w-full"
    wire:init="monitorIsReady"
    @if ($this->isReady && $this->hasDelegates)
        wire:poll.1s="pollDelegates"
    @elseif ($this->isReady)
        wire:poll.8s="pollDelegates"
    @endif
>
    <x-skeletons.delegates.monitor>
        <x-tables.desktop.delegates.monitor :delegates="$delegates" />

        <x-tables.mobile.delegates.monitor :delegates="$delegates" />
    </x-skeletons.delegates.monitor>
</div>
