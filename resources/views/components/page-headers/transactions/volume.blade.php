@props(['volume'])

<x-page-headers.header-item
    :title="trans('pages.transactions.volume_24h')"
    :attributes="$attributes"
>
    <span>
        {{ ExplorerNumberFormatter::currency($volume, Network::currency()) }}
    </span>
</x-page-headers.header-item>
