@props([
    'name',
    'model' => null,
    'label' => null,
])

@php
    $isSelected = false;
    if ($model && is_bool($this->{$model})) {
        $isSelected = $this->{$model} === true;
    } else {
        $isSelected = $this->filter[$name] === true;
    }
@endphp

<x-tables.filters.includes.item
    :attributes="$attributes->class('table-filter-item__checkbox')"
    :is-selected="$isSelected"
>
    <x-ark-checkbox
        :name="$name"
        :model="$model ?? 'filter.'.$name"
        :label="$label"
        :label-classes="Arr::toCssClasses([
            'text-base',
            'text-theme-secondary-900 dark:text-theme-secondary-200' => ! $isSelected,
            'text-theme-primary-600 font-semibold dark:hover:text-theme-dark-blue-600 transition-default' => $isSelected,
        ])"
        class=""
        wrapper-class="flex-1"
    />
</x-tables.filters.includes.item>
