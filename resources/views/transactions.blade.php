@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    <x-metadata page="transactions" />

    @section('content')
        <x-ark-container>
            <livewire:transaction-table />
        </x-ark-container>
    @endsection
@endcomponent
