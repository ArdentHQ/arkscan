@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="wallet" :detail="['address' => $wallet->isDelegate() ? $wallet->username() : $wallet->address()]" />

    @section('content')
        @if ($wallet->isMigration())
            <x-page-headers.migration-wallet :wallet="$wallet" />
        @else
            <x-page-headers.wallet :wallet="$wallet" />
        @endif

        @if($wallet->isVoting())
            <x-wallet.vote-for :vote="$wallet->vote()" :wallet="$wallet" />
        @endif

        <x-wallet.transactions :wallet="$wallet" />
    @endsection
@endcomponent
