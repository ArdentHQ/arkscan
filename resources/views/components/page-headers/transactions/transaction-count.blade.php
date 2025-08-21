@props(['count'])

<x-page-headers.header-item
    :title="trans('pages.transactions.transactions_24h')"
    :attributes="$attributes"
>
    <x-number>{{ $count }}</x-number>
</x-page-headers.header-item>
