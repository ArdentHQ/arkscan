@props(['block'])

<x-general.page-section.container :title="trans('pages.block.block_summary')">
    <x-block.page.section-detail.row
        :title="trans('pages.block.header.block_reward')"
        :value="ExplorerNumberFormatter::networkCurrency($block->reward(), withSuffix: true)"
    />

    <x-block.page.section-detail.row
        :title="trans('pages.block.header.total_fees')"
        :value="ExplorerNumberFormatter::networkCurrency($block->fee(), withSuffix: true)"
    />
</x-general.page-section.container>
