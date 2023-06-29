@props([
    'id',
    'variableName',
])

<x-ark-checkbox
    name="{{ $id }}"
    x-ref="{{ $id }}"
    x-model="{{ $variableName }}.{{ $id }}"
    :attributes="$attributes->class('dropdown__checkbox pl-4 transition-default select-none')"

    ::class="{
        'bg-theme-primary-50 dark:bg-theme-dark-900': {{ $variableName }}.{{ $id }} === true,
    }"

    label-classes="w-full text-base block py-3  cursor-pointer"
    alpine-label-class="{
        'text-theme-primary-600 dark:text-theme-dark-blue-500 font-semibold': {{ $variableName }}.{{ $id }} === true,
        'text-theme-secondary-900 dark:text-theme-dark-50': {{ $variableName }}.{{ $id }} === false,
    }"

    wrapper-class="flex-1"
    no-livewire
>
    <x-slot name="label">
        {{ $slot }}
    </x-slot>
</x-ark-checkbox>
