<x-wallet.heading.frame title="pages.voters_by_wallet.title" :wallet="$wallet">
    <x-wallet.heading.frame-item icon="app-transactions.vote" title="pages.wallet.delegate.voters">
        <x-number>{{ $wallet->voterCount() }}</x-number>
    </x-wallet.heading.frame-item>
</x-wallet.heading.frame>
