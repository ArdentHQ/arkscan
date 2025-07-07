@props(['count'])

<x-page-headers.header-item
    :title="trans('pages.blocks.max_transactions_24h')"
    :attributes="$attributes"
>
    <span>
        <x-number>{{ $count }}</x-number>
    </span>
</x-page-headers.header-item>
