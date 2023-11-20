@props([
    'label',
    'breakpoint' => 'md',
    'extra' => null,
])

@php
    $wrapperBreakpointClass = [
        'sm' => 'sm:items-center sm:rounded-lg sm:border sm:border-theme-secondary-300 sm:dark:border-theme-dark-800',
        'md' => 'md:items-center md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-dark-800',
    ][$breakpoint] ?? 'md:items-center md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-dark-800';

    $detailBreakpointClass = [
        'sm' => 'sm:flex-row sm:items-center sm:space-y-0 sm:space-x-3',
        'md' => 'md:flex-row md:items-center md:space-y-0 md:space-x-3',
    ][$breakpoint] ?? 'md:flex-row md:items-center md:space-y-0 md:space-x-3';

    $labelBreakpointClass = [
        'sm' => 'sm:px-4 sm:py-[14.5px] sm:bg-theme-secondary-200 sm:dark:bg-black sm:text-base sm:text-lg sm:!leading-5.25',
        'md' => 'md:px-4 md:py-[14.5px] md:bg-theme-secondary-200 md:dark:bg-black md:text-lg md:!leading-5.25',
    ][$breakpoint] ?? 'md:px-4 md:py-[14.5px] md:bg-theme-secondary-200 md:dark:bg-black md:text-lg md:!leading-5.25';

    $contentBreakpointClass = [
        'sm' => 'sm:leading-5.25 sm:text-lg',
        'md' => 'md:leading-5.25 md:text-lg',
    ][$breakpoint] ?? 'md:leading-5.25 md:text-lg';
@endphp

<div
    x-data="{}"
    {{ $attributes->class('flex flex-col px-6 pt-8 pb-6 md:px-10 md:mx-auto md:max-w-7xl') }}
>
    <div @class([
        'flex overflow-hidden flex-col space-y-4 font-semibold sm:flex-row sm:justify-between sm:items-end sm:space-y-0',
        $wrapperBreakpointClass,
    ])>
        <div @class([
            'flex flex-col min-w-0 space-y-2',
            $detailBreakpointClass,
        ])>
            <div @class([
                'text-sm dark:text-theme-dark-200 !leading-4.25 whitespace-nowrap',
                $labelBreakpointClass,
            ])>
                {{ $label }}
            </div>

            <div @class([
                'min-w-0 leading-5 text-theme-secondary-900 dark:text-theme-dark-200',
                $contentBreakpointClass,
            ])>
                {{ $slot }}
            </div>
        </div>

        @if ($extra)
            <div @class([
                'flex space-x-2 w-full sm:w-auto',
                'sm:px-4' => $breakpoint === 'sm',
                'md:px-4' => $breakpoint === 'md',
            ])>
                {{ $extra }}
            </div>
        @endif
    </div>
</div>
