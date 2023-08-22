@props([
    'value',
    'decimals' => 8,
])

<x-currency :currency="Network::currency()">
    @if ($decimals === 0)
        {{ number_format((float) ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation::execute((float) $value), $decimals) }}
    @else
        {{ rtrim(rtrim(number_format((float) ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation::execute((float) $value), $decimals), 0), '.') }}
    @endif
</x-currency>
