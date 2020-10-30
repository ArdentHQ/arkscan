<x-wallet.heading.frame title="pages.blocks_by_wallet.title" :wallet="$wallet">
    <x-wallet.heading.frame-item icon="app-block-id" title="pages.wallet.delegate.forged_blocks">
        <x-number>{{ $wallet->blocksForged() }}</x-number>
    </x-wallet.heading.frame-item>
</x-wallet.heading.frame>
