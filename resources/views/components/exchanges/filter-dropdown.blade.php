@props(['options', 'selected', 'icon', 'param'])

<x-exchanges.dropdown
    :title="trans($options[$selected])"
    :icon="$icon"
>
    @foreach ($options as $value => $label)
        <x-general.dropdown.list-item 
            :is-active="$selected === $value"
            wire:click="setFilter('{{ $param }}', '{{ $value }}')"
        >
            @lang($label)
        </x-general.dropdown.list-item>
    @endforeach
</x-exchanges.dropdown>
