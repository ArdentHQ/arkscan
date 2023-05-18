<x-general.dropdown.dropdown>
    <x-slot name="button" class="rounded border border-theme-secondary-300 dark:border-theme-secondary-800">
        <div class="flex justify-center items-center py-2 px-3 space-x-2 text-sm font-semibold leading-4 transition-default">
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

    <x-slot
        name="content"
        class="right-0 top-full"
    >
        @foreach (trans('pagination.per_page_options') as $perPage)
            <x-general.dropdown.list-item
                :is-active="$perPage === $this->perPage"
                wire:click="setPerPage({{ $perPage }})"
            >
                {{ $perPage }}
            </x-general.dropdown.list-item>
        @endforeach
    </x-slot>
</x-general.dropdown.dropdown>
