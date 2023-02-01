@component('layouts.app')
    <x-metadata page="wallets" />

    @section('content')
        @if(Network::hasMigration())
            <livewire:migration.wallet-highlight />
        @endif

        <x-ark-container>
            <livewire:wallet-table />
        </x-ark-container>
    @endsection
@endcomponent
