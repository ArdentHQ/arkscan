<div>
    @if($modalShown)
        <div
            x-ref="modal"
            x-data="Modal.livewire({
                searchType: '{{ $type ?? 'block' }}',
                showAdvanced: false,
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
                    const { input, inputMobile } = this.$refs;
                    const style = window.getComputedStyle(input);
                    if (style.display === 'none') {
                        inputMobile.focus();
                    } else {
                        input.focus();
                    }
                },
            })"
            class="container flex overflow-auto fixed inset-0 z-50 flex-col pt-24 mx-auto w-full h-screen outline-none md:overflow-visible md:px-8 md:pb-24"
            tabindex="0"
            data-modal
            wire:keydown.escape="closeModal"
            x-init="
                init();
                initSearch();
            "
            @search-type-changed.window="searchType = $event.detail"
        >
            <div wire:click.self="closeModal" class="fixed inset-0 opacity-70 dark:opacity-80 bg-theme-secondary-900 dark:bg-theme-secondary-800"></div>

            <div class="flex overflow-auto relative flex-col w-full md:overflow-visible md:px-8 content-container-full-width">
                <h2 class="px-8 mx-auto mb-10 text-3xl font-bold text-center text-white md:text-4xl">
                    @lang('pages.search.title')
                </h2>

                <div class="flex overflow-auto flex-col bg-white rounded-xl md:overflow-visible md:mb-14 dark:bg-theme-secondary-900">
                    <x-general.search.search-input />

                    <x-general.search.advanced-search
                        x-show="showAdvanced"
                        :transaction-options="$transactionOptions"
                        :type="$type ?? 'block'"
                        :state="$state"
                        class="overflow-auto md:overflow-visible"
                        x-cloak
                    />

                    <div
                        class="py-4 font-semibold text-center rounded-b-lg md:hidden bg-theme-primary-100 text-theme-primary-600 dark:bg-theme-secondary-800 dark:text-theme-secondary-200"
                        @click="showAdvanced = !showAdvanced"
                    >
                        <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
                        <span x-show="showAdvanced">@lang('actions.hide_advanced')</span>
                    </div>
                </div>
            </div>
        </div>
   @endif
</div>

