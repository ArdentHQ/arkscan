@props([
    'id',
    'variableName',
])

<x-ark-checkbox
    name="id"
    x-ref="{{ $id }}"
    alpine="(e) => {{ $variableName }}['{{ $id }}'] = e.target.checked"
    :label-classes="Arr::toCssClasses([
        '',
        // 'text-theme-secondary-900 dark:text-theme-secondary-200' => ! $isSelected,
        // 'text-theme-primary-600 font-semibold dark:group-hover:text-theme-dark-blue-600 transition-default' => $isSelected,
    ])"
    class="py-3 pr-6 pl-5 leading-5 border-l-4 cursor-pointer transition-default dark:hover:bg-theme-secondary-900 dark:text-theme-secondary-200 hover:text-theme-secondary-900 hover:bg-theme-secondary-200"
    wrapper-class="flex-1"
    no-livewire
>
    <x-slot name="label">
        {{ $slot }}
    </x-slot>
</x-ark-checkbox>




{{-- <
    x-ref="{{ $id }}"
    @click="() => {
        {{ $variableName }} = '{{ $id }}';
        isOpen = false;
    }"
    :class="{
        'border-theme-primary-600 bg-theme-primary-50 dark:bg-theme-secondary-900 text-theme-primary-600 dark:text-white font-semibold': {{ $variableName }} === '{{ $id }}',
        'border-transparent text-theme-secondary-900': {{ $variableName }} !== '{{ $id }}',
    }"

    {{ $attributes->class('border-l-4 pl-5 pr-6 py-3 hover:text-theme-secondary-900 hover:bg-theme-secondary-200 dark:hover:bg-theme-secondary-900 dark:text-theme-secondary-200 transition-default cursor-pointer leading-5') }}
>
    {{ $slot }}
</a> --}}
