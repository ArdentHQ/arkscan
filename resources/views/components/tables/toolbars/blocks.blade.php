@props(['blocks'])

<x-tables.toolbars.toolbar :result-count="$blocks->total()">
    <div class="flex space-x-3">
        <div class="flex-1 sm:flex-none">
            <button
                type="button"
                class="flex justify-center items-center space-x-2 w-full sm:py-1.5 sm:px-4 button-secondary"
                disabled
            >
                <x-ark-icon
                    name="arrows.underline-arrow-down"
                    size="sm"
                />

                <div>@lang('actions.export')</div>
            </button>
        </div>
    </div>
</x-general.encapsulated.table-header>
