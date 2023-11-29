<x-general.dropdown.dropdown
    active-button-class=""
    button-class="bg-white rounded border border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-800"
    disabled-button-class="text-theme-secondary-500 bg-theme-secondary-200 dark:text-theme-dark-500 dark:bg-theme-dark-800 dark:border-theme-dark-700"
    :attributes="$attributes"
>
    <x-slot name="button">
        <div @class([
            'flex justify-center items-center py-2 px-3 space-x-2 text-sm font-semibold leading-4 transition-default',
            'dark:text-theme-dark-50' => $attributes->get('disabled', false) === false,
            'dark:bg-theme-dark-800' => $attributes->get('disabled', false) === true,
        ])>
            <span>{{ $this->perPage }}</span>

            <span
                class="transition-default"
                :class="{ 'rotate-180': dropdownOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-3 h-3"
                />
            </span>
        </div>
    </x-slot>

    @foreach ($this->perPageOptions() as $perPage)
        <x-general.dropdown.list-item
            :is-active="$perPage === $this->perPage"
            wire:click="setPerPage({{ $perPage }})"
        >
            {{ $perPage }}
        </x-general.dropdown.list-item>
    @endforeach
</x-general.dropdown.dropdown>
