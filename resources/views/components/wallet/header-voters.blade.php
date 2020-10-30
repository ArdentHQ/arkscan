<x-wallet.header-frame title="pages.voters_by_wallet.title" :wallet="$wallet">
    <x-wallet.header-frame-item icon="app-transactions.vote" title="pages.wallet.delegate.voters">
        <x-number>{{ $wallet->voterCount() }}</x-number>
    </x-wallet.header-frame-item>
</x-wallet.header-frame>
