<div class="w-full">
    <x-tables.toolbars.validators.list-table :validators="$validators" />

    <div class="sm:hidden">
        <x-validators.resigned-validator-notice />
    </div>

    <x-skeletons.validators.list-table
        :row-count="$this->perPage"
        :paginator="$validators"
    >
        <x-tables.desktop.validators.list-table
            :validators="$validators"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.validators.list-table
            :validators="$validators"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.validators.list-table>

    <x-general.pagination.table :results="$validators" />
</div>
