@props([
    'id',
    'variableName',
])

<x-ark-checkbox
    name="{{ $id }}"
    x-ref="{{ $id }}"
    x-model="{{ $variableName }}.{{ $id }}"
    class="pl-3 border-l-4 dropdown__checkbox"

    ::class="{
        'border-theme-primary-600 bg-theme-primary-50 dark:bg-theme-secondary-900 text-theme-primary-600 dark:text-white font-semibold': {{ $variableName }}.{{ $id }} === true,
        'border-transparent text-theme-secondary-900': {{ $variableName }}.{{ $id }} === false,
    }"

    label-classes="w-full block py-3 group-hover:text-theme-secondary-900 group-hover:bg-theme-secondary-200 dark:group-hover:bg-theme-secondary-900 dark:text-theme-secondary-200 transition-default cursor-pointer"

    wrapper-class="flex-1"
    no-livewire
>
    <x-slot name="label">
        {{ $slot }}
    </x-slot>
</x-ark-checkbox>
