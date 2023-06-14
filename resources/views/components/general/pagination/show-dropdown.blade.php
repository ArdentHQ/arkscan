<x-general.dropdown.dropdown>
    <x-slot name="button" class="bg-white rounded border border-theme-secondary-300 dark:bg-theme-secondary-900 dark:border-theme-secondary-800">
        <div class="flex justify-center items-center py-2 px-3 space-x-2 text-sm font-semibold leading-4 transition-default dark:text-theme-secondary-200">
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

    @foreach (trans('pagination.per_page_options') as $perPage)
        <x-general.dropdown.list-item
            :is-active="$perPage === $this->perPage"
            wire:click="setPerPage({{ $perPage }})"
        >
            {{ $perPage }}
        </x-general.dropdown.list-item>
    @endforeach
</x-general.dropdown.dropdown>
