@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.home.title')" />
        <meta property="og:description" content="@lang('metatags.home.description')">
    @endpush

    @section('content')
        <x-general.search.header />

        @if(Settings::usesCharts())
            <div class="content-container">
                <div class="flex-col hidden w-full divide-y sm:flex divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-700">
                    <div class="flex flex-col w-full pt-16 space-x-0 lg:flex-row lg:space-x-10">
                        @if(Settings::usesPriceChart())
                            <x-charts.price :data="$prices" identifier="price" colours-scheme="#339A51" />
                        @endif

                        @if(Settings::usesFeeChart())
                            <x-charts.price :data="$fees" identifier="fees" colours-scheme="#FFAE10" />
                        @endif
                    </div>

                    <div class="grid w-full grid-flow-row grid-cols-2 gap-6 pt-8 mt-5 mb-16 {{ Network::canbeExchanged() ?  'xl:grid-cols-4' : 'xl:grid-cols-3'}} gap-y-12 xl:gap-y-4">
                        @if(Network::canBeExchanged())
                            <x-details-box :title="trans('pages.home.network-details.price')" :value="$aggregates['price']" icon="app-price" />
                        @endif
                        <x-details-box :title="trans('pages.home.network-details.lifetime_transactions_volume')" :value="$aggregates['volume']" icon="app-volume" />
                        <x-details-box :title="trans('pages.home.network-details.lifetime_transactions')" :value="$aggregates['transactionsCount']" icon="app-transactions-amount" />
                        <x-details-box :title="trans('pages.home.network-details.total_votes')" :value="$aggregates['votesCount']" :extra-value="$aggregates['votesPercentage']" icon="app-votes" />
                    </div>
                </div>
            </div>
        @endif

        <x-home.content />
    @endsection

@endcomponent
