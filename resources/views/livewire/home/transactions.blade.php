<div class="w-full">
    <x-skeletons.home.transactions :row-count="$this->perPage">
        <x-tables.desktop.home.transactions
            :transactions="$transactions"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.home.transactions
            :transactions="$transactions"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.footers.view-all
            :results="$transactions"
            :count-suffix="trans('tables.home.transactions')"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.transactions>
</div>
