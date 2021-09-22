<x-page-headers.transaction.icon-type :model="$transaction" />

<x-general.entity-header-item
    :title="trans('pages.transaction.unvote')"
    :avatar="$transaction->unvoted()->username()"
>
    <x-slot name="text">
        <a href="{{ route('wallet', $transaction->unvoted()->address()) }}" class="font-semibold link">
            {{ $transaction->unvoted()->username() }}
        </a>
    </x-slot>
</x-general.entity-header-item>

<x-general.entity-header-item
    :title="trans('pages.transaction.fee')"
    icon="app-monitor"
>
    <x-slot name="text">
        <x-general.amount-fiat-tooltip
            :amount="$transaction->fee()"
            :fiat="$transaction->feeFiat()"
        />
    </x-slot>
</x-general.entity-header-item>

<x-general.entity-header-item
    :title="trans('pages.transaction.confirmations')"
    icon="app-confirmations"
>
    <x-slot name="text">
        <x-number>{{ $transaction->confirmations() }}</x-number>
    </x-slot>
</x-general.entity-header-item>
