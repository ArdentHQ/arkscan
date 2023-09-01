@props(['amount'])

<x-page-headers.header-item
    :title="trans('pages.blocks.largest_block_24h')"
    :attributes="$attributes"
>
    <span>
        {{ ExplorerNumberFormatter::currency($amount, Network::currency()) }}
    </span>
</x-page-headers.header-item>
