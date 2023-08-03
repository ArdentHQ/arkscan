@component('layouts.app')
    <x-metadata page="delegates" />

    @section('content')
        <livewire:delegate-data-boxes />

        <x-ark-container>
            <livewire:delegates.tabs />
        </x-ark-container>
    @endsection
@endcomponent
