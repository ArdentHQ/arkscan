@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.home.title')" />
        <meta property="og:description" content="@lang('metatags.home.description')">
    @endpush

    @section('content')
        <x-general.search.header />

        @if(Settings::usesCharts())
            <div class="content-container">
                <div class="flex-col hidden w-full divide-y sm:flex divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
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
                            <x-details-box :title="trans('pages.home.network-details.price')" icon="app-price">
                                {{ $aggregates['price'] }}
                            </x-details-box>
                        @endif

                        <x-details-box :title="trans('pages.home.network-details.lifetime_transactions_volume')" icon="app-volume">
                            {{ $aggregates['volume'] }}
                        </x-details-box>

                        <x-details-box :title="trans('pages.home.network-details.lifetime_transactions')" icon="app-transactions-amount">
                            {{ $aggregates['transactionsCount'] }}
                        </x-details-box>

                        <x-details-box :title="trans('pages.home.network-details.total_votes')" :extra-value="$aggregates['votesPercentage']" icon="app-votes">
                            {{ $aggregates['votesCount'] }}
                        </x-details-box>
                    </div>
                </div>
            </div>
        @endif

        <x-home.content />
    @endsection

@endcomponent
