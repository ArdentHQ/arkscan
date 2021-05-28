<div
    wire:poll.60s
    class="uppercase"
    :class="{ 'opacity-50': busy }"
    x-data="{ to: '{{ $to }}', busy: false }"
    x-init="livewire.on('currencyChanged', () => busy = true);"
>
    {{ $from }}/{{ $to }}: {{ $price }}
</div>
