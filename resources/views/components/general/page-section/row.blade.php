@props([
    'title',
    'headerWidth' => 'w-[87px]',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
])

<div {{ $attributes->class('flex items-center space-x-4') }}>
    <div @class([
        'whitespace-nowrap',
        $headerWidth,
    ])>
        {{ $title }}
    </div>

    <div @class([
        'flex-1 space-y-3 text-right sm:text-left text-theme-secondary-900 dark:text-theme-dark-50',
        $valueClass,
    ])>
        @if ($value || strlen($slot) > 0)
            <span
                @if ($tooltip)
                    data-tippy-content="{{ $tooltip }}"
                @endif
            >
                @if ($value)
                    {{ $value }}
                @else
                    {{ $slot }}
                @endif
            </span>
        @else
            @lang('general.na')
        @endif
    </div>
</div>
