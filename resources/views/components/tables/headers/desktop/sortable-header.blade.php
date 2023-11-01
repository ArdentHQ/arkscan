@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
    'nameProperties' => [],
    'width' => null,
    'sortingId' => null,
    'initialSort' => 'asc',
    'livewireSort' => false,
    'sortDisabled' => false,
    'sortIconAlignment' => 'right',
    'hideSorting' => false,
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :width="$width"
    :class="Arr::toCssClasses([
        'group/header' => ! $hideSorting && $sortingId !== null,
        'cursor-pointer' => ! $hideSorting && $sortingId !== null && (($livewireSort && $this->isReady) || ! $livewireSort),
        'flex-row-reverse space-x-0' => ! $hideSorting && $livewireSort && $sortingId !== null,
        'disabled' => ! $hideSorting && $livewireSort && $sortingId !== null && ! $this->isReady,
        'group/header' => $sortingId !== null,
        'cursor-pointer' => $sortingId !== null && (($livewireSort && $this->isReady) || ! $livewireSort),
        'flex-row-reverse space-x-0' => $livewireSort && $sortingId !== null,
        'disabled' => $sortDisabled || ($livewireSort && $sortingId !== null && ! $this->isReady),
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => $sortingId,
        'x-on:click' => ! $hideSorting && ! $livewireSort && $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => ! $hideSorting && ! $livewireSort && $sortingId !== null ? $initialSort : null,
        'wire:loading.class' => $sortDisabled || (! $hideSorting && $livewireSort && $sortingId !== null) ? 'disabled' : null,
        'wire:loading.class.remove' => ! $hideSorting && $livewireSort && $sortingId !== null ? 'cursor-pointer' : null,
    ])"
>
    @if ($hideSorting)
        <span>@lang($name, $nameProperties)</span>

        @if ($slot->isNotEmpty())
            {{ $slot }}
        @endif
    @else
        <button
            type="button"
            @class([
                'py-3 -my-3',
                'flex w-full items-center space-x-2' => $sortingId !== null,
                'justify-end' => $sortIconAlignment === 'left' && $sortingId !== null,
            ])

            @if ($sortDisabled || ($livewireSort && $sortingId !== null))
                wire:click="sortBy('{{ $sortingId }}')"
                wire:loading.attr="disabled"

                @if ($sortDisabled || ! $this->isReady)
                    disabled
                @endif
            @endif
        >
            @if (! $livewireSort && $sortIconAlignment === 'left')
                <x-tables.headers.desktop.includes.sort-icon
                    :id="$sortingId"
                    :initial-direction="$initialSort"
                    :disabled="$sortDisabled"
                />
            @endif

            <span>@lang($name, $nameProperties)</span>

            @if ($slot->isNotEmpty())
                {{ $slot }}
            @endif

            @if ($livewireSort)
                <x-tables.headers.desktop.includes.livewire-sort-icon :id="$sortingId" />
            @elseif ($sortIconAlignment === 'right')
                <x-tables.headers.desktop.includes.sort-icon
                    :id="$sortingId"
                    :initial-direction="$initialSort"
                    :disabled="$sortDisabled"
                />
            @endif
        </button>
    @endif
</x-ark-tables.header>
