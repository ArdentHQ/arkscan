@props([
    'forgedCount',
    'missedCount',
    'totalRewards',
    'maxTransactions',
])

<x-page-headers.generic
    :title="trans('pages.blocks.title')"
    :subtitle="trans('pages.blocks.subtitle', ['network' => Network::name()])"
    class="md:pb-3"
>
    <div class="grid flex-1 grid-cols-1 gap-2 w-full sm:grid-cols-2 md:gap-3 xl:grid-cols-4">
        <x-page-headers.blocks.blocks-produced :count="$forgedCount" />
        <x-page-headers.blocks.missed-blocks :count="$missedCount" />
        <x-page-headers.blocks.block-rewards :rewards="$totalRewards" />
        <x-page-headers.blocks.max-transactions :count="$maxTransactions" />
    </div>
</x-page-headers.generic>
