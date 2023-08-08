@component('layouts.app')
    <x-metadata page="delegates" />

    @section('content')
        <livewire:delegates.header-stats />

        <x-ark-container>
            <livewire:delegates.tabs />
        </x-ark-container>
    @endsection
@endcomponent
