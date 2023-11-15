<div class="w-full">
    <x-tables.toolbars.delegates.recent-votes :votes="$votes" />

    <x-skeletons.delegates.recent-votes
        :row-count="$this->perPage"
        :paginator="$votes"
    >
        <x-tables.desktop.delegates.recent-votes
            :votes="$votes"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.delegates.recent-votes
            :votes="$votes"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.delegates.recent-votes>

    <x-general.pagination.table :results="$votes" />
</div>
