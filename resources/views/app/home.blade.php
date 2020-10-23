@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.home.title')" />
        <meta property="og:description" content="@lang('metatags.home.description')">
    @endpush

    @section('content')
        <x-general.search.header />

        @if(Settings::usesCharts())
            <div class="justify-center py-16 content-container">
                @if(Settings::usesPriceChart())
                    <x-charts.price :data="$prices" identifier="price" colours-scheme="#339A51" />
                @endif

                @if(Settings::usesFeeChart())
                    <x-charts.price :data="$fees" identifier="fees" colours-scheme="#FFAE10" />
                @endif
            </div>
        @endif

        <x-home.content />
    @endsection

@endcomponent
