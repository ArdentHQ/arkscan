@props([
    'transactionCount',
    'volume',
    'totalFees',
    'averageFee',
])

<x-page-headers.generic
    :title="trans('pages.transactions.title')"
    :subtitle="trans('pages.transactions.subtitle', ['network' => Network::name()])"
>
    <div class="grid flex-1 grid-cols-1 gap-2 w-full sm:grid-cols-2 md:gap-3 xl:grid-cols-4">
        <x-page-headers.transactions.transaction-count :count="$transactionCount" />
        <x-page-headers.transactions.volume :volume="$volume" />
        <x-page-headers.transactions.total-fees :total-fees="$totalFees" />
        <x-page-headers.transactions.average-fee :average-fee="$averageFee" />
    </div>
</x-page-headers.generic>
