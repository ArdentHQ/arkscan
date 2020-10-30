@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('metatags')
        <meta property="og:title" content="@lang('metatags.wallets.title')" />
        <meta property="og:description" content="@lang('metatags.wallets.description')">
    @endsection

    @section('content')
        <x-general.search.header />

        <x-wallets.sorted-by-balance />
    @endsection

@endcomponent
