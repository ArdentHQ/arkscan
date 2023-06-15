@props(['transactions'])

<x-tables.toolbars.toolbar :result-count="$transactions->total()">
    <div class="flex space-x-3">
        <button
            type="button"
            class="flex flex-1 justify-center items-center space-x-2 sm:flex-none sm:py-1.5 sm:px-4 button-secondary"
        >
            <x-ark-icon
                name="arrows.underline-arrow-down"
                size="sm"
            />

            <div>@lang('actions.export')</div>
        </button>

        <button
            type="button"
            class="flex flex-1 justify-center items-center sm:flex-none sm:py-1.5 sm:px-4 md:p-2 button-secondary"
        >
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="ml-2 md:hidden">
                @lang('actions.filter')
            </div>
        </button>
    </div>
</x-general.encapsulated.table-header>
