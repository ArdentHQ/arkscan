@props(['options', 'selected', 'icon', 'param'])

<x-exchanges.dropdown
    :title="trans($options[$selected])"
    :icon="$icon"
>
    @foreach ($options as $value => $label)
        @if ($value === $selected)
            @continue
        @endif

        <x-general.dropdown.list-item  wire:click="setFilter('{{ $param }}', '{{ $value }}')">
            @lang($label)
        </x-general.dropdown.list-item>
    @endforeach
</x-exchanges.dropdown>
