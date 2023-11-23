<div class="w-full">
    <x-tables.toolbars.delegates.list-table :delegates="$delegates" />

    <x-skeletons.delegates.list-table
        :row-count="$this->perPage"
        :paginator="$delegates"
    >
        <x-tables.desktop.delegates.list-table
            :delegates="$delegates"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.delegates.list-table
            :delegates="$delegates"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.delegates.list-table>

    <x-general.pagination.table :results="$delegates" />
</div>
