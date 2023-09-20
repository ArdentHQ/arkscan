@props([
    'value',
    'decimals' => 8,
])

<x-currency :currency="Network::currency()">
    @if ($decimals === 0)
        {{ number_format((float) ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation::execute((float) $value), $decimals) }}
    @else
        {{ ExplorerNumberFormatter::unformattedRawValue($value, $decimals) }}
    @endif
</x-currency>
