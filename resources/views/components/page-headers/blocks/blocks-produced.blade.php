@props(['count'])

<x-page-headers.header-item
    :title="trans('pages.blocks.blocks_produced_24h')"
    :attributes="$attributes"
>
    <span>
        <x-number>{{ $count }}</x-number>
    </span>
</x-page-headers.header-item>
