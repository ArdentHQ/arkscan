@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.home.title')" />
        <meta property="og:description" content="@lang('metatags.home.description')">
    @endpush

    @section('content')
        {{-- <x-general.search.header />

        @if(Settings::usesCharts())
            <x-home.charts :prices="$prices" :fees="$fees" :aggregates="$aggregates" />
        @endif --}}

        <x-home.content />
    @endsection

@endcomponent
