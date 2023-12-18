@props([
    'value',
    'decimals' => 8,
])

<x-currency :currency="Network::currency()">
    {{ $value }}
</x-currency>
