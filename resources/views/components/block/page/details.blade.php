@props(['block'])

<x-general.page-section.container :title="trans('pages.block.block_details')">
    <x-block.page.section-detail.row
        :title="trans('pages.block.header.timestamp')"
        :value="$block->timestamp()"
        :block="$block"
    />

    <x-block.page.section-detail.row :title="trans('pages.block.header.height')">
        <x-number>{{ $block->height() }}</x-number>
    </x-block.page.section-detail.row>

    <x-block.page.section-detail.row
        :title="trans('pages.block.header.transactions')"
        :value="$block->transactionCount()"
        allow-empty
    />
</x-general.page-section.container>
