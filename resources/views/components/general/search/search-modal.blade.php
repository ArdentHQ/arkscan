<div>
    @if($modalShown)
        <div
            x-ref="modal"
            x-data="Modal.livewire({
                query: @entangle('query'),
                searching: false,
                initSearch() {
                    this.$nextTick(() => {
                        this.focusSearchInput();
                    });
                },
                getScrollable() {
                    const { searchResults } = this.$refs;
                    return searchResults;
                },
                focusSearchInput(){
                    const { input } = this.$refs;
                    input.focus();
                },
            }, { disableFocusTrap: true })"
            class="container flex overflow-auto fixed inset-0 z-50 flex-col mx-auto w-full h-screen outline-none md:hidden custom-scroll"
            tabindex="0"
            data-modal
            wire:keydown.escape="closeModal"
            x-init="
                init();
                initSearch();
            "
        >
            <div wire:click.self="closeModal" class="fixed inset-0 opacity-70 dark:opacity-80 bg-theme-secondary-900 dark:bg-theme-secondary-800"></div>

            <div class="flex relative flex-col p-6 my-6 mx-4 bg-white rounded-xl border border-transparent sm:m-8 dark:bg-theme-secondary-900 dark:text-theme-secondary-200 dark:border-theme-secondary-800">
                <x-general.search.search-input />

                <div x-ref="searchResults" class="flex flex-col space-y-1 text-sm font-semibold whitespace-nowrap divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
                    @if ($hasResults && $results !== null)
                        @foreach ($results as $result)
                            <div wire:key="{{ $result->id() }}" class="pt-1">
                                @if (is_a($result->model(), \App\Models\Wallet::class))
                                    <x-search.results.wallet :wallet="$result" truncate :truncate-length="14" />
                                @elseif (is_a($result->model(), \App\Models\Block::class))
                                    <x-search.results.block :block="$result" />
                                @elseif (is_a($result->model(), \App\Models\Transaction::class))
                                    <x-search.results.transaction :transaction="$result" />
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="mt-4 text-center whitespace-normal dark:text-theme-secondary-500">
                            <p x-show="query">@lang('general.navbar.no_results')</p>
                            <p x-show="!query">@lang('general.search.results_will_show_up')</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
   @endif
</div>

