@props([
    'title',
    'headerWidth' => 'w-[87px]',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
    'allowEmpty' => false,
])

<div {{ $attributes->class('flex flex-col space-y-2 md:space-y-0 md:flex-row md:items-center md:space-x-4 first:pt-0 pt-3 md:pt-0') }}>
    <div @class([
        'whitespace-nowrap',
        $headerWidth,
    ])>
        {{ $title }}
    </div>

    <div @class([
        'flex-1 space-y-3 md:text-right text-theme-secondary-900 dark:text-theme-dark-50',
        $valueClass,
    ])>
        @if ($value || strlen($slot) > 0 || $allowEmpty)
            <span
                @if ($tooltip)
                    data-tippy-content="{{ $tooltip }}"
                @endif
            >
                @if ($value || ($allowEmpty && $value === 0))
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
