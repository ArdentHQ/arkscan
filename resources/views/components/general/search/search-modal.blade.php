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
                    const { advancedSearch } = this.$refs;
                    return advancedSearch;
                },
                focusSearchInput(){
                    const { input } = this.$refs;
                    input.focus();
                },
            }, { disableFocusTrap: true })"
            class="container flex overflow-auto fixed inset-0 z-50 flex-col mx-auto w-full h-screen outline-none md:hidden"
            tabindex="0"
            data-modal
            wire:keydown.escape="closeModal"
            x-init="
                init();
                initSearch();
            "
        >
            <div wire:click.self="closeModal" class="fixed inset-0 opacity-70 dark:opacity-80 bg-theme-secondary-900 dark:bg-theme-secondary-800"></div>

            <div class="flex overflow-auto relative flex-col mx-4 my-6 p-6 bg-white rounded-xl dark:bg-theme-secondary-900">

                <x-general.search.search-input />

                <div class="flex flex-col space-y-4 whitespace-nowrap divide-y divide-dashed divide-theme-secondary-300 text-sm font-semibold overflow-auto custom-scroll">
                    @if ($hasResults && $results !== null)
                        @foreach ($results as $result)
                            @if (is_a($result->model(), \App\Models\Wallet::class))
                                <x-search.navbar.wallet :wallet="$result" />
                            @elseif (is_a($result->model(), \App\Models\Block::class))
                                <x-search.navbar.block :block="$result" />
                            @elseif (is_a($result->model(), \App\Models\Transaction::class))
                                <x-search.navbar.transaction :transaction="$result" />
                            @endif
                        @endforeach
                    @else
                        <div class="whitespace-normal text-center mt-4">
                            <p x-show="query">@lang('general.navbar.no_results')</p>
                            <p x-show="!query">Results will show up here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
   @endif
</div>

