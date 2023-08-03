@props([
    'header',
    'expandable' => false,
])

<div
    @if ($expandable)
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
            'space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700 sm:rounded-b-none' => $expandable,
        ]) }}

        @if (strlen($slot) > 0 && $expandable)
            :class="{
                'rounded-b': ! isExpanded,
            }"
        @endif
    >
        {{ $header }}

        @if ($expandable)
            <div class="pl-4 h-[17px] flex items-center sm:hidden">
                <x-general.dropdown.arrow
                    key="isExpanded"
                    x-on:click="isExpanded = ! isExpanded"
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

            class="flex flex-col px-4 pt-3 pb-4 space-y-4 sm:flex-row sm:space-y-0 sm:flex-1 sm:justify-between"
        >
            {{ $slot }}
        </div>
    @endif
</div>
