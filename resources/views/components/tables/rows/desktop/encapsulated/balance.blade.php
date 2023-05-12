@props([
    'model'
])

<span @if(Network::canBeExchanged()) data-tippy-content="{{ $model->balanceFiat() }}" @endif>
    <span>{{ rtrim(rtrim(number_format((float) ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation::execute((float) $model->balance()), 8), 0), '.') }}</span> {{-- TODO: take decimals from network --}}
</span>
