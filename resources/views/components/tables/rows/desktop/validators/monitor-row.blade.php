@props([
    'validator',
    'wireKey' => null,
])

<x-ark-tables.row
    x-data="Validator('{{ $validator->address() }}')"
    wire:key="validator-{{ $validator->order() }}-{{ $validator->wallet()->address() }}-{{ $validator->roundNumber() }}-{{ microtime(true) }}"
    ::class="{
        'validator-monitor-favorite': isFavorite === true,
    }"
>
    <x-ark-tables.cell
        ::data-value="isFavorite ? 1 : 0"
        width="20"
    >
        <x-validators.favorite-toggle :model="$validator" />
    </x-ark-tables.cell>

    <x-ark-tables.cell
        width="60"
        data-value="{{ $validator->order() }}"
    >
        <span class="text-sm font-semibold leading-4.25">
            {{ $validator->order() }}
        </span>
    </x-ark-tables.cell>

    <x-ark-tables.cell>
        <div class="flex items-center space-x-2">
            <x-tables.rows.desktop.encapsulated.address
                :model="$validator->wallet()"
                without-clipboard
                :validator-name-class="Arr::toCssClasses([
                    'md-lg:w-auto',
                    'md:w-[200px]' => ! $validator->keepsMissing(),
                ])"
            />

            <x-validators.missed-warning :validator="$validator->wallet()" />
        </div>
    </x-ark-tables.cell>

    <x-ark-tables.cell
        breakpoint="md-lg"
        responsive
        class="w-[374px]"
    >
        <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status :model="$validator" />
    </x-ark-tables.cell>

    <x-ark-tables.cell
        class="md-lg:hidden"
        breakpoint="md"
        responsive
    >
        <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status
            :model="$validator"
            with-time
        />
    </x-ark-tables.cell>

    <x-ark-tables.cell
        breakpoint="md-lg"
        responsive
        width="160"
    >
        <x-tables.rows.desktop.encapsulated.validators.monitor.time-to-forge :model="$validator" />
    </x-ark-tables.cell>

    <x-ark-tables.cell class="text-right" width="100">
        <x-tables.rows.desktop.encapsulated.validators.monitor.block-height :model="$validator" />
    </x-ark-tables.cell>
</x-ark-tables.row>
