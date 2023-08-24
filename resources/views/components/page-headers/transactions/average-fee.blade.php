@props(['averageFee'])

<x-page-headers.header-item
    :title="trans('pages.transactions.average_fee_24h')"
    :attributes="$attributes"
>
    <span>
        {{ ExplorerNumberFormatter::currency($averageFee, Network::currency()) }}
    </span>
</x-page-headers.header-item>
