<div class="w-full" wire:init="setIsReady">
    <x-tables.toolbars.validators.recent-votes :votes="$votes" />

    <x-skeletons.validators.recent-votes
        :row-count="$this->perPage"
        :paginator="$votes"
    >
        <x-tables.desktop.validators.recent-votes
            :votes="$votes"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.validators.recent-votes
            :votes="$votes"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.validators.recent-votes>

    <x-general.pagination.table :results="$votes" />
</div>
