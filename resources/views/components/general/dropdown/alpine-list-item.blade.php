@props([
    'id',
    'variableName',
])

<a
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
</a>
