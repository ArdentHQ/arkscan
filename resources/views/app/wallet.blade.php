@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('content')
        <x-page-headers.wallet :wallet="$wallet" />

        @if($wallet->isDelegate())
            <x-wallet.delegate :wallet="$wallet" />
        @endif

        @if($wallet->hasRegistrations())
            <x-wallet.registrations :wallet="$wallet" />
        @endif

        <x-wallet.transactions :wallet="$wallet" />
    @endsection

@endcomponent
