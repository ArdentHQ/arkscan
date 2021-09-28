@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    <x-metadata page="wallets" />

    @section('content')
        <x-wallets.sorted-by-balance />
    @endsection
@endcomponent
