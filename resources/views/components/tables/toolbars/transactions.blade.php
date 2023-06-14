@props(['transactions'])

<x-tables.toolbars.toolbar :result-count="$transactions->count()">
    <div class="flex space-x-3">
        <button
            type="button"
            class="button-secondary flex items-center space-x-2 sm:py-1.5 sm:px-4 flex-1 sm:flex-none justify-center"
        >
            <x-ark-icon
                name="arrows.underline-arrow-down"
                size="sm"
            />

            <div>@lang('actions.export')</div>
        </button>

        <button
            type="button"
            class="md:p-2 button-secondary flex items-center sm:py-1.5 sm:px-4 flex-1 sm:flex-none justify-center"
        >
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="md:hidden ml-2">
                @lang('actions.filter')
            </div>
        </button>
    </div>
</x-general.encapsulated.table-header>
