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
