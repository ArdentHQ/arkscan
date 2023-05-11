@component('layouts.app')
    <x-metadata page="wallets" />

    @section('content')
        <x-ark-container>
            <x-compatible-wallets.header />

            <x-compatible-wallets.section-divider />

            <x-compatible-wallets.arkvault />

            <x-compatible-wallets.section-divider />

            <x-compatible-wallets.wallet-grid />

            <x-compatible-wallets.list-wallet />
        </x-ark-container>
    @endsection
@endcomponent
