@props([
    'transaction',
    'valueClass' => null,
])

<x-tables.rows.mobile.encapsulated.cell :attributes="$attributes">
    <x-slot name="label">
        <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-200">
            <x-general.encapsulated.transaction-type :transaction="$transaction" />
        </div>
    </x-slot>

    @php($votedValidator = $transaction->voted())

    @if ($votedValidator)
        <a
            href="{{ route('wallet', $votedValidator->address()) }}"
            class="text-sm font-semibold link"
        >
            @if ($votedValidator->hasUsername())
                {{ $votedValidator->username() }}
            @else
                <x-truncate-middle>
                    {{ $votedValidator->address() }}
                </x-truncate-middle>
            @endif
        </a>
    @endif
</x-tables.rows.mobile.encapsulated.cell>
