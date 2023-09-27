@props([
    'title',
    'disabled' => false,
])

<div {{ $attributes->class('space-y-2') }}>
    <div class="text-sm font-semibold dark:text-theme-dark-200 leading-4.25">
        {{ $title }}
    </div>

    <div @class([
        'font-semibold leading-5',
        'text-theme-secondary-900 dark:text-theme-secondary-50' => ! $disabled,
        'text-theme-secondary-500 dark:text-theme-secondary-700' => $disabled,
    ])>
        @unless ($disabled)
            {{ $slot }}
        @else
            @lang('general.na')
        @endunless
    </div>
</div>
