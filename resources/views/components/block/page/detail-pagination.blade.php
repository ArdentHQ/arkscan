@props(['block'])

@php
    $previousUrl = $block->previousBlockUrl();
    $nextUrl     = $block->nextBlockUrl();
@endphp

<div {{ $attributes->class('px-3 sm:px-6 md:px-10 md:mx-auto md:max-w-7xl group last:mb-8 dark:text-theme-dark-200 mt-2') }}>
    <div class="flex sm:justify-end space-x-2">
        <a
            @if ($nextUrl !== null)
                href="{{ $nextUrl }}"
            @endif

            @class([
                'py-1.5 px-4 font-semibold flex-1 sm:flex-none',
                'button-secondary' => $nextUrl !== null,
                'button-generic bg-theme-secondary-200 text-theme-secondary-500 dark:bg-theme-secondary-800 dark:text-theme-secondary-700 select-none' => $nextUrl === null,
            ])
        >
            <div class="flex items-center justify-center space-x-2">
                <x-ark-icon name="arrows.chevron-left-small" size="w-3 h-3" />

                <div>
                    <span class="hidden sm:inline">Previous Block</span>
                    <span class="inline sm:hidden">Previous</span>
                </div>
            </div>
        </a>

        <a
            @if ($previousUrl !== null)
                href="{{ $previousUrl }}"
            @endif

            @class([
                'py-1.5 px-4 font-semibold flex-1 sm:flex-none',
                'button-secondary' => $previousUrl !== null,
                'button-generic bg-theme-secondary-200 text-theme-secondary-500 dark:bg-theme-secondary-800 dark:text-theme-secondary-700 select-none' => $previousUrl === null,
            ])
        >
            <div class="flex items-center justify-center space-x-2">
                <div>
                    <span class="hidden sm:inline">Next Block</span>
                    <span class="inline sm:hidden">Next</span>
                </div>

                <x-ark-icon name="arrows.chevron-right-small" size="w-3 h-3" />
            </div>
        </a>
    </div>
</div>
