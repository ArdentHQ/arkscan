<x-page-headers.wallet.frame title="pages.blocks_by_wallet.title" :wallet="$wallet" use-generator>
    <x-page-headers.wallet.frame-item icon="app-block-id" :title="trans('pages.wallet.delegate.forged_blocks')">
        <x-number>{{ $wallet->blocksForged() }}</x-number>
    </x-page-headers.wallet.frame-item>
</x-page-headers.wallet.frame>
