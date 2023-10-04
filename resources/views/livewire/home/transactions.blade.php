<div
    class="w-full"
    @if ($this->isReady)
        wire:poll.10s
    @endif
>
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
            :route="route('transactions', ['page' => 2])"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.transactions>
</div>
