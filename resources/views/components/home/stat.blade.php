@props([
    'title',
    'disabled' => false,
])

<div {{ $attributes->class('space-y-2') }}>
    <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-200">
        {{ $title }}
    </div>

    <div @class([
        'font-semibold !leading-5 text-sm md:text-base',
        'text-theme-secondary-900 dark:text-theme-dark-50' => ! $disabled,
        'text-theme-secondary-500 dark:text-theme-dark-700' => $disabled,
    ])>
        @unless ($disabled)
            {{ $slot }}
        @else
            @lang('general.na')
        @endunless
    </div>
</div>
