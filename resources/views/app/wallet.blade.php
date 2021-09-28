@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="wallet" :detail="['address' => $wallet->isDelegate() ? $wallet->username() : $wallet->address()]" />

    @section('content')
        <x-page-headers.wallet :wallet="$wallet" />

        @if($wallet->isVoting())
            <x-wallet.vote-for :vote="$wallet->vote()" :wallet="$wallet" />
        @endif

        <x-wallet.transactions :wallet="$wallet" />
    @endsection
@endcomponent
