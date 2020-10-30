@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.wallet.title')" />
        <meta property="og:description" content="@lang('metatags.wallet.description')">
    @endpush

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('content')
        <x-wallet.heading.wallet :wallet="$wallet" />

        @if($wallet->isDelegate())
            <x-wallet.delegate :wallet="$wallet" />
        @endif

        @if($wallet->hasRegistrations())
            <x-wallet.registrations :wallet="$wallet" />
        @endif

        <x-wallet.transactions :wallet="$wallet" />
    @endsection

@endcomponent
