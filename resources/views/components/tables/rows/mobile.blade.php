@props([
    'header',
    'expandable' => false,
    'expandClass' => 'space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700',
    'expandDisabled' => false,
    'contentClass' => null,
    'xData' => null,
])

<div
    @if ($xData)
        x-data="{{ $xData }}"
    @elseif ($expandable)
        x-data="{
            isExpanded: false,
        }"
    @endif

    {{ $attributes->class('text-sm rounded border border-theme-secondary-300 dark:border-theme-dark-700') }}
>
    <div
        {{ $header->attributes->class([
            'flex justify-between items-center rounded-t bg-theme-secondary-100 dark:bg-theme-dark-950',
            $header->attributes->get('padding', 'py-3 px-4'),
            'rounded-b' => strlen($slot) === 0 && ! $expandable,
            'sm:rounded-b-none' => $expandable,
            $expandClass => $expandable,
        ]) }}

        @if (strlen($slot) > 0 && $expandable)
            :class="{
                'rounded-b': ! isExpanded,
            }"
        @endif
    >
        {{ $header }}

        @if ($expandable)
            <div class="flex items-center pl-3 sm:hidden h-[17px]">
                <x-general.dropdown.arrow
                    key="isExpanded"
                    x-on:click="{{ $expandDisabled ? '' : 'isExpanded = ! isExpanded' }}"
                    :color="Arr::toCssClasses([
                        'text-theme-secondary-700 dark:text-theme-dark-200' => ! $expandDisabled,
                        'text-theme-secondary-300 dark:text-theme-dark-800' => $expandDisabled,
                    ])"
                />
            </div>
        @endif
    </div>

    @if (strlen($slot) > 0)
        <div
            @if ($expandable)
                :class="{
                    'hidden sm:flex': ! isExpanded,
                }"
                x-transition
                x-cloak
            @endif

            @class([
                'flex flex-col px-4 pt-3 pb-4 space-y-4 sm:flex-row sm:flex-1 sm:justify-between sm:space-y-0',
                $contentClass,
            ])
        >
            {{ $slot }}
        </div>
    @endif
</div>
