<x-page-headers.wallet.frame title="pages.voters_by_wallet.title" :wallet="$wallet">
    <x-page-headers.wallet.frame-item icon="app-transactions.vote" :title="trans('pages.wallet.delegate.voters')">
        <x-number>{{ $wallet->voterCount() }}</x-number>
    </x-page-headers.wallet.frame-item>
    <x-page-headers.wallet.frame-item icon="double-check-mark" :title="trans('pages.wallet.delegate.votes_percentage', [number_format($wallet->votesPercentage(), 2)])">
        <x-currency :currency="Network::currency()">{{ $wallet->votes() }}</x-currency>
    </x-page-headers.wallet.frame-item>
</x-page-headers.wallet.frame>
