<div class="flex flex-col space-y-8 sm:flex-row sm:space-y-0 xl:pr-7 xl:border-r border-theme-secondary-300 dark:border-theme-secondary-800">
    <x-page-headers.transaction.icon-type :model="$transaction" wrapper-class="sm:w-1/2" />

    <x-general.entity-header-item
        :title="trans('pages.transaction.amount')"
        icon="app-supply"
        :truncate="false"
    >
        <x-slot name="text">
            <x-general.amount-fiat-tooltip
                :amount="$transaction->amount()"
                :fiat="$transaction->amountFiat()"
            />
        </x-slot>
    </x-general.entity-header-item>
</div>

<div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2 xl:pl-7">
    <x-general.entity-header-item
        :title="trans('pages.transaction.fee')"
        :wrapper-class="$wrapperClass ?? ''"
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
        :wrapper-class="$wrapperClass ?? ''"
        icon="app-confirmations"
    >
        <x-slot name="text">
            <x-number>{{ $transaction->confirmations() }}</x-number>
        </x-slot>
    </x-general.entity-header-item>
</div>
