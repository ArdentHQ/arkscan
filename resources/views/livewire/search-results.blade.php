<div>
    <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
        <div class="flex-col py-16 content-container md:px-8">
            <h1 class="mb-4 header-2">@lang('pages.search_results.title')</h1>

            @if($state['results'] && $state['results']->count())
                <div>
                    @if ($state['type'] === 'block')
                        <livewire:tables.blocks :blocks="$state['results']" />
                    @endif

                    @if ($state['type'] === 'transaction')
                        <livewire:tables.transactions :transactions="$state['results']" />
                    @endif

                    @if ($state['type'] === 'wallet')
                        <livewire:tables.wallets :wallets="$state['results']" />
                    @endif
                </div>
            @else
                <div class="flex flex-col justify-center pt-8 space-y-8">
                    <img src="/images/search/empty.svg" class="h-32" />

                    <span class="text-center">@lang('pages.search_results.no_results')</span>
                </div>
            @endif
        </div>
    </div>
</div>
