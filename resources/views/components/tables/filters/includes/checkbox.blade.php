@props([
    'name',
    'label' => null,
])

<x-tables.filters.includes.item :attributes="$attributes">
    <x-ark-checkbox
        :name="$name"
        :label="$label"
        label-classes="text-theme-secondary-900 dark:text-theme-secondary-200 text-base"
        class=""
    />
</x-tables.filters.includes.item>
