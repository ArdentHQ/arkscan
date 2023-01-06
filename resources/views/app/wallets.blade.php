@component('layouts.app')
    <x-metadata page="wallets" />

    @section('content')
        <livewire:migration.wallet-highlight />

        <x-ark-container>
            <livewire:wallet-table />
        </x-ark-container>
    @endsection
@endcomponent
