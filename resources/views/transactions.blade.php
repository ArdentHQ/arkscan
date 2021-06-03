@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-ark-container>
            <livewire:transaction-table />
        </x-ark-container>
    @endsection

@endcomponent
