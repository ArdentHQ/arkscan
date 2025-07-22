@props(['validators'])

<div
    x-show="tab === 'validators'"
    id="validators-list"
    {{ $attributes->class('w-full') }}
>
    {{-- <livewire:validators.validators /> --}}

    <div class="w-full">
        <x-tables.toolbars.validators.list-table :validators="$validators" />

        <div class="sm:hidden">
            <x-validators.arkconnect.resigned-validator-notice />
            <x-validators.arkconnect.standby-validator-notice />
        </div>

        <x-skeletons.validators.list-table
            :row-count="$this->perPage"
            :paginator="$validators"
        >
            <x-tables.desktop.validators.list-table
                :validators="$validators"
                :no-results-message="$this->validatorsNoResultsMessage"
            />

            <x-tables.mobile.validators.list-table
                :validators="$validators"
                :no-results-message="$this->validatorsNoResultsMessage"
            />
        </x-skeletons.validators.list-table>

        <x-general.pagination.table :results="$validators" />
    </div>
</div>
