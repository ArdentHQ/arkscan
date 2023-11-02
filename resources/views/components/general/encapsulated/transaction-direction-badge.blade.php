@props([
    'width' => 'w-[39px]',
])

<x-general.badge :attributes="$attributes->class([
    'text-center encapsulated-badge',
    $width,
])">
    {{ $slot }}
</x-general.badge>
