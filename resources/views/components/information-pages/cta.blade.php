@props([
    'text',
    'breakpoint' => 'sm',
])

@php
    $containerBreakpoint = [
        'sm' => 'sm:flex-row sm:py-2 sm:text-start space-y-3 sm:space-y-0',
        'md' => 'md:flex-row md:py-2 md:text-start space-y-3 md:space-y-0',
    ][$breakpoint] ?? 'sm:flex-row sm:py-2 sm:text-start';

    $textBreakpoint = [
        'sm' => 'sm:text-lg',
        'md' => 'md:text-lg',
    ][$breakpoint] ?? 'sm:text-lg';
@endphp

<div @class([
    'flex flex-col justify-between items-center py-6 px-6 mt-6 w-full text-center rounded-xl bg-theme-primary-100 dark:bg-theme-secondary-800',
    $containerBreakpoint,
])>
    <span @class([
        'font-semibold  dark:text-white text-theme-primary-900',
        $textBreakpoint,
    ])>
        {!! $text !!}
    </span>

    {{ $slot }}
</div>
