<div>
    @if($state['results'])
        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="flex-col py-16 content-container md:px-8">
                <h1 class="header-2">@lang('pages.search_results.title')</h1>

                <div class="mt-4">
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
            </div>
        </div>
    @endif
</div>
