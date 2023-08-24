@props([
    'title' => null,
    'background' => null,
    'contentClass' => null,
])

<div {{ $attributes->class('relative font-semibold flex-1 rounded md:rounded-xl overflow-hidden z-10') }}>
    @if ($background !== null)
        <div {{ $background->attributes->class('absolute z-20') }}>
            {{ $background }}
        </div>
    @endif

    <div @class([
        'relative flex flex-col rounded md:rounded-xl leading-5',
        'ring-1 ring-inset ring-theme-secondary-300 dark:ring-theme-dark-700 px-4 py-3 md:px-6 md:py-4' => $background === null,
        'h-full p-4 md:px-6 md:py-4' => $background !== null,
        'space-y-2' => $title !== null,
        $contentClass,
    ])>
        @if ($title)
            <div class="text-sm leading-4.25 dark:text-theme-dark-200">
                {{ $title }}
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
