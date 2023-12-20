@props([
    'id',
    'variableName',
    'inactiveClass' => null,
])

<a
    x-ref="{{ $id }}"
    @click="() => {
        {{ $variableName }} = '{{ $id }}';
        isOpen = false;
    }"
    :class="{
        'border-theme-primary-600 dark:border-theme-dark-blue-500 bg-theme-primary-50 dark:bg-theme-dark-900 text-theme-primary-600 dark:text-theme-dark-blue-500 font-semibold': {{ $variableName }} === '{{ $id }}',
        'border-transparent text-theme-secondary-900 hover:text-theme-secondary-900 hover:bg-theme-secondary-100 hover:dark:bg-theme-dark-900': {{ $variableName }} !== '{{ $id }}',
        '{{ $inactiveClass }}': {{ $variableName }} !== '{{ $id }}'
    }"

    {{ $attributes->class('border-l-4 pl-5 pr-6 py-3 dark:text-theme-dark-200 transition-default cursor-pointer leading-5') }}
>
    {{ $slot }}
</a>
