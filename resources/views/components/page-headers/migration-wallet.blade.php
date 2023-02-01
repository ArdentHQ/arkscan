<x-page-headers.wallet.migration-frame title="pages.wallet.title" :wallet="$wallet">
    <x-page-headers.wallet.frame-item icon="wallet" title-class="whitespace-nowrap">
        <x-slot name="title">
            <div class="flex">
                <span>@lang('pages.wallet.migrated_amount')</span>

                @if(Network::canBeExchanged())
                    <span class="mr-1">:</span>

                    <livewire:wallet-balance :wallet="$wallet->model()" />
                @endif
            </div>
        </x-slot>

        <x-currency :currency="Network::currency()">{{ $wallet->balance() }}</x-currency>

        <x-slot name="extension">
            <div class="hidden items-center pl-3 ml-auto sm:flex">
                <x-page-headers.wallet.actions.qr-code :wallet="$wallet" />
            </div>
        </x-slot>
    </x-page-headers.wallet.frame-item>
</x-page-headers.wallet.migration-frame>
