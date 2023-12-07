@props([
    'title' => null,
    'background' => null,
    'contentClass' => null,
    'slotClass' => null,
    'withoutPadding' => false,
])

<div {{ $attributes->class('relative font-semibold flex-1 rounded md:rounded-xl overflow-hidden z-10') }}>
    @if ($background !== null)
        <div {{ $background->attributes->class('absolute z-20') }}>
            {{ $background }}
        </div>
    @endif

    <div @class([
        'relative flex flex-col rounded md:rounded-xl',
        'ring-1 ring-inset ring-theme-secondary-300 dark:ring-theme-dark-700 px-4 py-3 md:px-6 md:py-4 dark:text-theme-dark-50' => $background === null,
        'h-full p-4 md:px-6 md:py-4' => ! $withoutPadding && $background !== null,
        'space-y-2' => $title !== null,
        $contentClass,
    ])>
        @if ($title)
            <div class="text-sm dark:text-theme-dark-200">
                {{ $title }}
            </div>
        @endif

        <div @class([
            'text-theme-secondary-900 dark:text-theme-dark-50 leading-5',
            $slotClass,
        ])>
            {{ $slot }}
        </div>
    </div>
</div>
