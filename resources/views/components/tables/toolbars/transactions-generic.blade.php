@props(['transactions'])

<x-tables.toolbars.toolbar
    :result-count="$transactions->total()"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            <x-tables.filters.transactions-generic />

            <x-tables.filters.transactions-generic mobile />
        </div>
    </div>
</x-tables.toolbars.toolbar>
