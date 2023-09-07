@props(['transaction'])

<x-general.badge :attributes="$attributes->class('encapsulated-badge')">
    <x-general.encapsulated.transaction-type :transaction="$transaction" />
</x-general.badge>
