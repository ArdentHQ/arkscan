<div
    wire:poll.60s
    class="uppercase"
    :class="{ 'opacity-50': busy }"
    x-data="{ to: '{{ $to }}', busy: false }"
    x-init="livewire.on('currencyChanged', () => busy = true);"
>
    {{ $from }}/{{ $to }}:
    @if($isAvailable)
        {{ $price }}
    @else
        <span class="dark:text-theme-secondary-600 text-theme-secondary-500">-</span>
    @endif
</div>
