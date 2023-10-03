@component('layouts.app')
    <x-metadata page="home" />

    @section('content')
        <x-home.header />

        <x-ark-container>
            <livewire:home.tables />

            <x-home.footer />
        </x-ark-container>
    @endsection
@endcomponent
