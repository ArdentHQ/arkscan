@props(['totalFees'])

<x-page-headers.header-item
    :title="trans('pages.transactions.total_fees_24h')"
    :attributes="$attributes"
>
    <span>
        {{ ExplorerNumberFormatter::currency($totalFees, Network::currency()) }}
    </span>
</x-page-headers.header-item>
