<div>
    <div class="bg-theme-secondary-100 dark:bg-black">
        <div class="content-container-full-width md:py-16 md:px-8">
            <div class="flex flex-col w-full space-y-5">
                <div class="flex items-center justify-between">
                    <div class="hidden text-2xl font-bold whitespace-no-wrap text-theme-secondary-900 lg:block dark:text-theme-secondary-200">
                        @lang('general.search_explorer')
                    </div>

                    <livewire:network-status-block />
                </div>

                <div class="px-8 md:px-0">
                    <livewire:search-module :is-advanced="true" :type="$state['type']" />
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900" id="results-list">
        <div class="flex-col py-16 content-container md:px-8">
            <h1 class="header-2">@lang('pages.search_results.title')</h1>

            @if($results && $results->count())
                <div>
                    @if ($state['type'] === 'block')
                        <x-tables.blocks :blocks="$results" />
                    @endif

                    @if ($state['type'] === 'transaction')
                        <x-tables.transactions :transactions="$results" />
                    @endif

                    @if ($state['type'] === 'wallet')
                        <x-tables.wallets :wallets="$results" />
                    @endif
                </div>
            @else
                <div class="flex flex-col justify-center pt-8 space-y-8">
                    <x-general.empty-search-image />

                    <span class="text-center">@lang('pages.search_results.no_results')</span>
                </div>
            @endif
        </div>
    </div>

    <script>
        window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#results-list')));
    </script>
</div>
