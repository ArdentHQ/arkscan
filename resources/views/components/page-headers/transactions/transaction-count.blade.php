@props(['count'])

<x-page-headers.header-item
    :title="trans('pages.transactions.transactions_24h')"
    :attributes="$attributes"
>
    <span>{{ $count }}</span>
</x-page-headers.header-item>
