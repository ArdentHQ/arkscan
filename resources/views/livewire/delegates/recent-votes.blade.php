<div class="w-full">
    <x-tables.toolbars.delegates.recent-votes :votes="$votes" />

    <x-skeletons.delegates.recent-votes :row-count="$this->perPage">
        <x-tables.desktop.delegates.recent-votes
            :votes="$votes"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.delegates.recent-votes
            :votes="$votes"
            :no-results-message="$this->noResultsMessage"
        />

        <x-general.pagination.table
            :results="$votes"
            class="mt-4 md:mt-0"
        />
    </x-skeletons.delegates.recent-votes>
</div>