@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-wallets.sorted-by-balance />
    @endsection

@endcomponent
