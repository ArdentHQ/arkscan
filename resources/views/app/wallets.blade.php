@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.top_wallets.title')" />
        <meta property="og:description" content="@lang('metatags.top_wallets.description')">
    @endpush

    @push('scripts')
        <script src="{{ mix('js/tippy.js')}}" defer></script>
    @endpush

    @section('content')
        <x-general.search.header />

        <x-wallets.sorted-by-balance />
    @endsection

@endcomponent
