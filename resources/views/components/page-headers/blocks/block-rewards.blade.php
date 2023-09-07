@props(['rewards'])

<x-page-headers.header-item
    :title="trans('pages.blocks.block_rewards_24h')"
    :attributes="$attributes"
>
    <span>
        {{ ExplorerNumberFormatter::currency($rewards, Network::currency()) }}
    </span>
</x-page-headers.header-item>
