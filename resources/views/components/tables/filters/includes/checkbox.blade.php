@props([
    'name',
    'model' => null,
    'label' => null,
])

@php
    $isSelected = false;
    // if ($model && is_bool($this->{$model})) {
    if ($model && is_bool($this->getPropertyValue($model))) {
        $isSelected = $this->getPropertyValue($model) === true;
    } else {
        $isSelected = $this->filter[$name] === true;
    }

@endphp

<div x-data="{
    selected: @entangle($model ?? 'filter.'.$name).live,
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
            label-classes="text-base transition-default"
            alpine-label-class="{
                'text-theme-secondary-900 font-normal dark:text-theme-dark-200': ! selected,
                'text-theme-primary-600 font-semibold dark:group-hover:text-theme-dark-blue-600': selected,
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
