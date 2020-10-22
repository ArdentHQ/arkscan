@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.home.title')" />
        <meta property="og:description" content="@lang('metatags.home.description')">
    @endpush

    @section('content')
        <x-general.search.header />

        <div class="justify-center py-16 content-container">
            <x-charts.price identifier="price" colours-scheme="#339A51" />
        </div>

        <x-home.content />
    @endsection

@endcomponent
