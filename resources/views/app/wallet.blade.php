@component('layouts.app')
    <x-metadata page="wallet" :detail="['address' => $wallet->isKnownWallet() ? $wallet->walletName() : $wallet->address()]" />

    @section('content')
        <x-page-headers.wallet :wallet="$wallet" />

        <x-wallet.overview :wallet="$wallet" />

        <x-wallet.transactions :wallet="$wallet" />
    @endsection

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                hideTableTooltipsOnLivewireEvent(/^wallet-((tables)|((block|voter)-table))$/);
            });
        </script>
    @endpush
@endcomponent
