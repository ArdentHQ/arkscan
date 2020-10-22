<div>
    @if($state['type'])
        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="flex-col py-16 content-container md:px-8">
                <h1 class="header-2">@lang('pages.search_results.title')</h1>

                <div class="mt-4">
                    @if ($state['type'] === 'block')
                        <livewire:block-table />
                    @endif

                    @if ($state['type'] === 'transaction')
                        <livewire:transaction-table />
                    @endif

                    @if ($state['type'] === 'wallet')
                        <livewire:wallet-table />
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
