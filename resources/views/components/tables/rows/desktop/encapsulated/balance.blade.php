@props([
    'model'
])

<span
    {{ $attributes->class('text-sm leading-4.25') }}
    @if(Network::canBeExchanged())
        data-tippy-content="{{ $model->balanceFiat() }}"
    @endif
>
    {{-- TODO: take decimals from network --}}
    <span>{{ ExplorerNumberFormatter::currencyWithoutSuffix($model->balance(), Network::currency()) }}</span>
</span>
