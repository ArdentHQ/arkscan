@component('layouts.app')
    <x-metadata page="home" />

    @section('content')
        <x-home.header />

        <x-ark-container>
            <livewire:latest-records />
        </x-ark-container>
    @endsection
@endcomponent
