@props([
    'delegate',
    'wireKey' => null,
])

<x-ark-tables.row
    x-data="Delegate('{{ $delegate->publicKey() }}')"
    wire:key="delegate-{{ $delegate->order() }}-{{ $delegate->wallet()->address() }}-{{ $delegate->roundNumber() }}-{{ microtime(true) }}"
    ::class="{
        'delegate-monitor-favorite': isFavorite === true,
    }"
>
    <x-ark-tables.cell
        ::data-value="isFavorite ? 1 : 0"
        width="20"
    >
        <x-delegates.favorite-toggle :model="$delegate" />
    </x-ark-tables.cell>

    <x-ark-tables.cell
        width="60"
        data-value="{{ $delegate->order() }}"
    >
        <span class="text-sm font-semibold leading-4.25">
            {{ $delegate->order() }}
        </span>
    </x-ark-tables.cell>

    <x-ark-tables.cell>
        <div class="flex items-center space-x-2">
            <x-tables.rows.desktop.encapsulated.address
                :model="$delegate->wallet()"
                without-clipboard
                :delegate-name-class="Arr::toCssClasses(['md-lg:w-auto',
                    'md:w-[200px]' => ! $delegate->keepsMissing(),
                ])"
            />

            <x-delegates.missed-warning :delegate="$delegate->wallet()" />
        </div>
    </x-ark-tables.cell>

    <x-ark-tables.cell
        breakpoint="md-lg"
        responsive
        class="w-[180px] xl:w-[374px]"
    >
        <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status :model="$delegate" />
    </x-ark-tables.cell>

    <x-ark-tables.cell
        class="w-[180px] md-lg:hidden"
        breakpoint="md"
        responsive
    >
        <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status
            :model="$delegate"
            with-time
        />
    </x-ark-tables.cell>

    <x-ark-tables.cell
        breakpoint="md-lg"
        responsive
        class="w-[160px]"
    >
        <x-tables.rows.desktop.encapsulated.delegates.monitor.time-to-forge :model="$delegate" />
    </x-ark-tables.cell>

    <x-ark-tables.cell class="text-right w-[100px]">
        <x-tables.rows.desktop.encapsulated.delegates.monitor.block-height :model="$delegate" />
    </x-ark-tables.cell>
</x-ark-tables.row>
