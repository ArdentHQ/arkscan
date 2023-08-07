@props(['transaction'])

<x-general.badge :attributes="$attributes->class('encapsulated-transaction-type')">
    <x-general.encapsulated.transaction-type :transaction="$transaction" />
</x-general.badge>
