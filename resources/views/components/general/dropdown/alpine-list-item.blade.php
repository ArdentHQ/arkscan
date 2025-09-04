@props([
    'id',
    'variableName',
    'activeClass' => 'bg-theme-secondary-200 dark:bg-theme-dark-950 text-theme-primary-600 dark:text-theme-dark-50',
    'inactiveClass' => 'text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50 hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950',
])

<a
    x-ref="{{ $id }}"
    @click="() => {
        {{ $variableName }} = '{{ $id }}';
        isOpen = false;
    }"
    :class="{
        '{{ $activeClass }}': {{ $variableName }} === '{{ $id }}',
        '{{ $inactiveClass }}': {{ $variableName }} !== '{{ $id }}',
    }"

    {{ $attributes->class('font-semibold inline-flex justify-between items-center px-5 py-[0.875rem] my-[0.125rem] transition-default cursor-pointer leading-5 rounded-lg') }}
>
    <span x-ref="{{ $id }}_value">{{ $slot }}</span>

    <span
        x-show="{{ $variableName }} === '{{ $id }}'"
        x-cloak
    >
        <x-ark-icon
            name="double-check-mark"
            size="sm"
            class="text-theme-primary-600 dark:text-theme-dark-50"
        />
    </span>
</a>
