@props([
    'value',
    'decimals' => 8,
])

<x-currency :currency="Network::currency()">
    {{ rtrim(rtrim(number_format((float) ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation::execute((float) $value), $decimals), 0), '.') }}
</x-currency>
