@props([
    'value',
    'decimals' => 8,
])

<x-currency
    :currency="Network::currency()"
    :decimals="$decimals"
>
    {{ $value }}
</x-currency>
