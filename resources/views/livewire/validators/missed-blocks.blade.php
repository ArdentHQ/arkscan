<div class="w-full">
    <x-tables.toolbars.validators.missed-blocks :blocks="$blocks" />

    <x-skeletons.validators.missed-blocks
        :row-count="$this->perPage"
        :paginator="$blocks"
    >
        <x-tables.desktop.validators.missed-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />

        <x-tables.mobile.validators.missed-blocks
            :blocks="$blocks"
            :no-results-message="$this->noResultsMessage"
        />
    </x-skeletons.validators.missed-blocks>

    <x-general.pagination.table :results="$blocks" />
</div>
