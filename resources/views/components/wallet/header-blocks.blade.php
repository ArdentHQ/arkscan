<x-wallet.header-frame title="pages.blocks_by_wallet.title" :wallet="$wallet">
    <x-wallet.header-frame-item icon="app-block-id" title="pages.wallet.delegate.forged_blocks">
        <x-number>{{ $wallet->blocksForged() }}</x-number>
    </x-wallet.header-frame-item>
</x-wallet.header-frame>
