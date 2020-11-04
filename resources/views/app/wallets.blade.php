@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-general.search.header />

        <x-wallets.sorted-by-balance />
    @endsection

@endcomponent
