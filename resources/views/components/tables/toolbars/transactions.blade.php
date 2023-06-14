@props(['transactions'])

<x-tables.toolbars.toolbar :result-count="$transactions->count()">
    <div class="flex space-x-3">
        <div class="flex-1 sm:flex-none">
            <button
                type="button"
                class="button-secondary flex items-center space-x-2 sm:py-1.5 sm:px-4 justify-center w-full"
            >
                <x-ark-icon
                    name="arrows.underline-arrow-down"
                    size="sm"
                />

                <div>@lang('actions.export')</div>
            </button>
        </div>

        <x-tables.filters.transactions />
    </div>
</x-general.encapsulated.table-header>
