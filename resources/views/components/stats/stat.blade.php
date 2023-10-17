@props([
    'label',
    'disabled'         => false,
    'padding'          => 'px-6 py-4',
    'textClass'        => null,
    'containerSpacing' => 'space-x-4',
])

<div {{ $attributes->class(['flex border border-theme-secondary-300 dark:border-theme-dark-700 rounded md:rounded-xl', $padding]) }}>
    <div @class([
        'flex flex-grow items-center',
        $containerSpacing,
    ])>
        <span @class([
            'flex flex-col flex-grow justify-between h-full space-y-2',
            $textClass,
        ])>
            <span class="text-sm font-semibold leading-4.25 whitespace-nowrap text-theme-secondary-700 dark:text-theme-dark-200">
                {{ $label }}
            </span>

            @if ($disabled)
                <span class="font-semibold leading-5 whitespace-nowrap text-theme-secondary-500 dark:text-theme-secondary-600">
                    @lang('general.not_available')
                </span>
            @else
                <span class="flex font-semibold leading-5 whitespace-nowrap dark:text-theme-secondary-50 text-theme-secondary-900 space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                    {{ $slot }}
                </span>
            @endif
        </span>
    </div>

    @isset($side)
        {{ $side }}
    @endisset
</div>
