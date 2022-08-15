@component('layouts.app')
    <x-metadata page="wallets" />

    @section('content')
        <x-ark-container>
            <livewire:wallet-table />
        </x-ark-container>
    @endsection
@endcomponent
