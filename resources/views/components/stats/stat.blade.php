@props([
    'label',
    'disabled'         => false,
    'textClass'        => null,
    'containerSpacing' => null,
])

<x-page-headers.header-item :title="$label">
    <div @class([
        'flex flex-col flex-grow space-y-2 justify-between flex-grow',
        $containerSpacing,
        $textClass,
    ])>
        @if ($disabled)
            <span class="font-semibold leading-5 whitespace-nowrap text-theme-secondary-500 dark:text-theme-dark-600">
                @lang('general.not_available')
            </span>
        @else
            <span class="flex space-x-3 text-sm font-semibold !leading-4.25 md:!leading-5 whitespace-nowrap divide-x md:text-base text-theme-secondary-900 divide-theme-secondary-300 dark:text-theme-dark-50 dark:divide-theme-dark-700">
                {{ $slot }}
            </span>
        @endif
    </div>

    @isset($side)
        {{ $side }}
    @endisset
</x-page-headers.header-item>
