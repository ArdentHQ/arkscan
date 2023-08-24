@props([
    'transactionCount',
    'volume',
    'totalFees',
    'averageFee',
])

<x-page-headers.generic
    :title="trans('pages.transactions.title')"
    :subtitle="trans('pages.transactions.subtitle')"
>
    <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2 md:gap-3">
        <x-page-headers.transactions.transaction-count :count="$transactionCount" />
        <x-page-headers.transactions.volume :volume="$volume" />
        <x-page-headers.transactions.total-fees :total-fees="$totalFees" />
        <x-page-headers.transactions.average-fee :average-fee="$averageFee" />
    </div>
</x-page-headers.generic>
