@component('layouts.app')
    <x-metadata page="migration" />

    @section('content')
        <x-page-headers.migration />

        <x-ark-container>
            <livewire:migration.transactions />
        </x-ark-container>
    @endsection
@endcomponent
