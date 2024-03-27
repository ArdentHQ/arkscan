@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="wallet" :detail="['address' => $wallet->hasUsername() ? $wallet->username() : $wallet->address()]" />

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
