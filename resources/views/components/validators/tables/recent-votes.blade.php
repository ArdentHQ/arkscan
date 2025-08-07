@props(['recentVotes'])

<div
    x-show="tab === 'recent-votes'"
    id="recent-votes-list"
    {{ $attributes->class('w-full') }}
>
    <div class="w-full">
        <x-tables.toolbars.validators.recent-votes :votes="$recentVotes" />

        <x-skeletons.validators.recent-votes
            :row-count="$this->perPage"
            :paginator="$recentVotes"
        >
            <x-tables.desktop.validators.recent-votes
                :votes="$recentVotes"
                :no-results-message="$this->recentVotesNoResultsMessage"
            />

            <x-tables.mobile.validators.recent-votes
                :votes="$recentVotes"
                :no-results-message="$this->recentVotesNoResultsMessage"
            />
        </x-skeletons.validators.recent-votes>

        <x-general.pagination.table :results="$recentVotes" />
    </div>

</div>
