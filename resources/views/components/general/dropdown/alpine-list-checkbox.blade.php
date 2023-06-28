@props([
    'id',
    'variableName',
])

<x-ark-checkbox
    name="{{ $id }}"
    x-ref="{{ $id }}"
    x-model="{{ $variableName }}.{{ $id }}"
    class="border-l-4 pl-5 pr-6 py-3 hover:text-theme-secondary-900 hover:bg-theme-secondary-200 dark:hover:bg-theme-secondary-900 dark:text-theme-secondary-200 transition-default cursor-pointer leading-5"

    ::class="{
        'border-theme-primary-600 bg-theme-primary-50 dark:bg-theme-secondary-900 text-theme-primary-600 dark:text-white font-semibold': {{ $variableName }}.{{ $id }} === true,
        'border-transparent text-theme-secondary-900': {{ $variableName }}.{{ $id }} === false,
    }"

    wrapper-class="flex-1"
    no-livewire
>
    <x-slot name="label">
        {{ $slot }}
    </x-slot>
</x-ark-checkbox>
