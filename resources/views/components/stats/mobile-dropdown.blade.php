<div
    wire:key="{{ Illuminate\Support\Str::random(20) }}"
    class="px-6 mb-5 md:hidden"
>
    <x-ark-dropdown
        wrapper-class="relative w-full rounded border md:w-1/2 border-theme-secondary-300 dark:text-theme-dark-200 dark:border-theme-dark-800"
        button-class="justify-between py-3 px-4 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-dark-200 dim:text-theme-dark-50"
        dropdown-content-classes="bg-white dark:bg-theme-dark-800 rounded-xl shadow-lg dark:shadow-none"
        dropdown-classes="left-0 w-full z-20"
    >
        <x-slot name="button">
            <div
                class="flex items-center"
                wire:ignore
            >
                @foreach (trans('pages.statistics.insights.dropdown') as $name => $text)
                    <div
                        x-show="tab === '{{ $name }}'"
                        x-cloak
                    >
                        {{ $text }}
                    </div>
                @endforeach
            </div>

            <span
                class="transition-default"
                :class="{ 'rotate-180': dropdownOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-3 h-3"
                    class="text-theme-secondary-700 dark:text-theme-dark-200"
                />
            </span>
        </x-slot>

        <div class="block justify-center items-center py-2">
            @foreach (trans('pages.statistics.insights.dropdown') as $name => $text)
                @if ($name === 'market_data' && !Network::canBeExchanged())
                    @continue
                @endif

                <a
                    wire:click="$set('view', '{{ $name }}');"
                    @click="view = '{{ $name }}'; tab = '{{ $name }}';"
                    class="dropdown-entry"
                    :class="{
                        'dropdown-entry-selected': tab === '{{ $name }}',
                    }"
                >
                    {{ $text }}
                </a>
            @endforeach
        </div>
    </x-ark-dropdown>
</div>
