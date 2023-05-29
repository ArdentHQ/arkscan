<div x-data="{
    query: @entangle('query'),
    dropdownOpen: @entangle('query'),
}">
    <x-ark-input-with-prefix
        icon="magnifying-glass"
        type="text"
        id="search"
        name="search"
        model="query"
        class="rounded-md border border-transparent focus-within:bg-white hover:bg-white w-[340px] bg-theme-secondary-200 group transition-default dark:bg-theme-secondary-900 focus-within:border-theme-primary-600 focus-within:dark:border-theme-secondary-700 hover:border-theme-primary-600 hover:dark:border-theme-secondary-700"
        :placeholder="trans('general.navbar.search_placeholder')"
        container-class="flex pl-1 border border-transparent dark:border-theme-secondary-800 group-hover:dark:border-theme-secondary-700 focus-within:border-theme-primary-600 focus-within:dark:border-theme-secondary-700 hover:border-theme-primary-600"
        wrapper-class-override="relative rounded"
        field-class-override="block w-full border-0 rounded outline-none appearance-none px-2 py-[7px] text-sm leading-4 placeholder:text-theme-secondary-700 text-theme-secondary-900 dark:text-theme-secondary-400 bg-transparent"
        hide-label
        disable-dirty-styling
        iconSize="sm"
    >
        <div
            class="flex items-center mr-4 space-x-4"
            x-show="query !== null && query !== ''"
            x-transition
        >
            <button
                type="button"
                wire:click="clear"
                class="p-2 -my-px bg-transparent button-secondary text-theme-secondary-700 dark:text-theme-secondary-600"
                x-cloak
            >
                <x-ark-icon
                    name="cross"
                    size="xs"
                />
            </button>

            <x-ark-icon
                name="square-return-arrow"
                class="hidden sm:block dark:text-theme-secondary-600"
                size="sm"
                x-cloak
            />
        </div>

        <x-ark-dropdown
            :init-alpine="false"
            :close-on-blur="false"
            dropdown-classes="w-[561px] top-9"
            dropdown-content-classes="bg-white rounded-xl shadow-lg dark:bg-theme-secondary-900 dark:text-theme-secondary-200 border border-transparent dark:border-theme-secondary-800"
        >
            <x-slot name="button">
            </x-slot>

            <div x-show="query" class="flex flex-col p-6 space-y-4 text-sm font-semibold whitespace-nowrap divide-y divide-dashed divide-theme-secondary-300">
                @if ($hasResults)
                    @foreach ($results as $result)
                        <div wire:key="{{ $result->model()->id }}" @class(['pt-4' => $loop->index > 0])>
                            @if (is_a($result->model(), \App\Models\Wallet::class))
                                <x-search.results.wallet :wallet="$result" />
                            @elseif (is_a($result->model(), \App\Models\Block::class))
                                <x-search.results.block :block="$result" />
                            @elseif (is_a($result->model(), \App\Models\Transaction::class))
                                <x-search.results.transaction :transaction="$result" />
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="px-4 -my-1">@lang('general.navbar.no_results')</p>
                @endif
            </div>
        </x-ark-dropdown>
    </x-ark-input-with-prefix>
</div>
