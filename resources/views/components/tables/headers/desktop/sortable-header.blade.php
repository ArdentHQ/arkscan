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
    'sortIconAlignment' => 'right',
])

<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :width="$width"
    :class="Arr::toCssClasses([
        'group/header' => $sortingId !== null,
        'cursor-pointer' => $sortingId !== null && (($livewireSort && $this->isReady) || ! $livewireSort),
        'flex-row-reverse space-x-0' => $livewireSort && $sortingId !== null,
        'disabled' => $livewireSort && $sortingId !== null && ! $this->isReady,
        $class,
    ])"
    :attributes="$attributes->merge([
        'x-ref' => $sortingId,
        'x-on:click' => ! $livewireSort && $sortingId !== null ? 'sortByColumn' : null,
        'data-initial-sort' => ! $livewireSort && $sortingId !== null ? $initialSort : null,
        'wire:loading.class' => $livewireSort && $sortingId !== null ? 'disabled' : null,
        'wire:loading.class.remove' => $livewireSort && $sortingId !== null ? 'cursor-pointer' : null,
    ])"
>
    <button
        type="button"
        @class([
            'py-3 -my-3',
            'flex w-full items-center space-x-2' => $sortingId !== null,
            'justify-end' => $sortIconAlignment === 'left' && $sortingId !== null,
        ])

        @if ($livewireSort && $sortingId !== null)
            wire:click="sortBy('{{ $sortingId }}')"
            wire:loading.attr="disabled"

            @unless ($this->isReady)
                disabled
            @endunless
        @endif
    >
        @if (! $livewireSort && $sortIconAlignment === 'left')
            <x-tables.headers.desktop.includes.sort-icon
                :id="$sortingId"
                :initial-direction="$initialSort"
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
            />
        @endif
    </button>
</x-ark-tables.header>
