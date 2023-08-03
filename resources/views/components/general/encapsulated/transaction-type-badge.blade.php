@props(['transaction'])

<x-general.badge :attributes="$attributes">
    <x-general.encapsulated.transaction-type :transaction="$transaction" />
</x-general.badge>
