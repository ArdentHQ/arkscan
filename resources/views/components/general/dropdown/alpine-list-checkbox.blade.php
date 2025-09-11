@props([
    'id',
    'variableName',
])

<x-ark-checkbox
    name="checkbox-{{ $id }}"
    x-ref="{{ $id }}"
    x-model="{{ $variableName }}.{{ $id }}"
    :attributes="$attributes->class([
        'dropdown__checkbox px-5 transition-default select-none rounded-lg font-semibold my-[0.125rem]',
    ])"

    label-classes="w-full text-base block cursor-pointer py-[0.875rem]"
    alpine-label-class="{
        'text-theme-primary-600 dark:text-theme-dark-50': {{ $variableName }}.{{ $id }} === true,
        'text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50': {{ $variableName }}.{{ $id }} === false,
    }"
    ::class="{
        'bg-theme-secondary-200 dark:bg-theme-dark-950 !text-theme-primary-600 !dark:text-theme-dark-50': {{ $variableName }}.{{ $id }} === true,
        'text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50 hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950': {{ $variableName }}.{{ $id }} === false,
    }"

    wrapper-class="flex-1"
    no-livewire
>
    <x-slot name="label">
        <div class="flex justify-between items-center">
            <div>
                {{ $slot }}
            </div>

            <span
                x-show="{{ $variableName }}.{{ $id }} === true"
                x-cloak
            >
                <x-ark-icon
                    name="double-check-mark"
                    size="sm"
                    class="text-theme-primary-600 dark:text-theme-dark-50"
                />
            </span>
        </div>
    </x-slot>
</x-ark-checkbox>
