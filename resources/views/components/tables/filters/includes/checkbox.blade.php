@props([
    'name',
    'model' => null,
    'label' => null,
])

@php
    $isSelected = false;
    if ($model && is_bool($this->getPropertyValue($model))) {
        $isSelected = $this->getPropertyValue($model) === true;
    } else {
        $isSelected = $this->getFilter($name) === true;
    }
@endphp

<div x-data="{
    selected: @entangle($model ?? 'filters.default.'.$name).live,
}">
    <x-tables.filters.includes.item
        :attributes="$attributes->class('table-filter-item__checkbox')"
        :is-selected="$isSelected"
        @click="(e) => {
            e.preventDefault();

            selected = ! selected;
        }"
    >
        <x-ark-checkbox
            :name="$name"
            no-livewire
            :label="$label"
            x-model="selected"
            label-classes="text-base transition-default font-semibold"
            alpine-label-class="{
                'text-theme-primary-600 dark:text-theme-dark-50': selected,
                'text-theme-secondary-700 dark:text-theme-dark-200 group-hover/filter-item:bg-theme-secondary-200 group-hover/filter-item:dark:bg-theme-dark-950 group-hover/filter-item:text-theme-secondary-900 group-hover/filter-item:dark:text-theme-dark-50': ! selected,
            }"
            class=""
            wrapper-class="flex-1"
            x-on:click="(e) => {
                Alpine.nextTick(() => {
                    e.target.checked = selected;
                });
            }"
        />
    </x-tables.filters.includes.item>
</div>
