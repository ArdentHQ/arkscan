@component('layouts.app')
    <x-metadata page="home" />

    @section('content')
        <livewire:network-status-block />

        <x-ark-container>
            <livewire:home.tables />
        </x-ark-container>
    @endsection
@endcomponent
