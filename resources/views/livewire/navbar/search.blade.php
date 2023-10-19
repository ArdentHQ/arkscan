@php ($isDisabled = app()->isDownForMaintenance())

<div
    x-ref="search"
    x-data="Search.setup({
        query: @entangle('query'),
        dropdownOpen: @entangle('query'),
    })"
    class="w-full"
>
    <x-ark-input-with-prefix
        icon="magnifying-glass"
        icon-class="text-theme-secondary-500 dark:text-theme-secondary-700"
        type="text"
        id="search"
        name="search"
        model="query"
        :class="Arr::toCssClasses([
            'rounded-md border border-transparent w-[340px] md:w-full md-lg:w-[340px] group transition-default bg-theme-secondary-200',
            'dark:bg-theme-secondary-800 cursor-not-allowed' => $isDisabled,
            'focus-within:bg-white hover:bg-white dark:bg-theme-secondary-900 focus-within:border-theme-primary-600 focus-within:dark:border-theme-primary-600 hover:[&:not(:focus-within)]:border-theme-primary-600 hover:[&:not(:focus-within)]:dark:border-theme-secondary-700' => ! $isDisabled,
        ])"
        :placeholder="trans('general.navbar.search_placeholder')"
        :container-class="Arr::toCssClasses([
            'flex pl-1 border border-transparent dark:border-theme-secondary-800',
            'cursor-not-allowed' => $isDisabled,
            'group-hover:[&:not(:focus-within)]:dark:border-theme-secondary-700 focus-within:border-theme-primary-600 focus-within:dark:border-theme-primary-600 hover:[&:not(:focus-within)]:border-theme-primary-600' => ! $isDisabled,
        ])"
        wrapper-class-override="relative rounded"
        :field-class-override="Arr::toCssClasses([
            'block w-full border-0 rounded outline-none appearance-none px-2 py-[7px] text-sm leading-4 bg-transparent',
            'placeholder:text-theme-secondary-500 text-theme-secondary-500 dark:placeholder:text-theme-secondary-700 cursor-not-allowed' => $isDisabled,
            'placeholder:text-theme-secondary-700 text-theme-secondary-900 dark:text-theme-secondary-400' => ! $isDisabled,
        ])"
        hide-label
        disable-dirty-styling
        icon-size="sm"
        wire:keydown.enter="goToFirstResult"
        wire:keydown.escape="clear"
        x-on:blur="blurHandler"
        autocomplete="off"
        :disabled="$isDisabled"
    >
        <div
            class="flex items-center mr-4 space-x-4"
            x-show="query !== null && query !== ''"
            x-transition
            x-cloak
        >
            <button
                type="button"
                wire:click="clear"
                class="p-2 -my-px bg-transparent button-secondary text-theme-secondary-700 dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                x-cloak
                x-on:blur="blurHandler"
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
            without-button
        >
            <div
                x-show="query"
                class="flex overflow-y-auto flex-col py-3 px-6 space-y-1 text-sm font-semibold whitespace-nowrap divide-y divide-dashed divide-theme-secondary-300 custom-scroll max-h-[410px] dark:divide-theme-secondary-800"
            >
                @if ($hasResults)
                    @foreach ($results as $result)
                        <div
                            wire:key="{{ $result->id() }}"
                            @class([
                                'select-none',
                                'pt-1' => $loop->index > 0,
                            ])
                        >
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
                    <p class="text-center dark:text-theme-secondary-500">
                        @lang('general.navbar.no_results')
                    </p>
                @endif
            </div>
        </x-ark-dropdown>
    </x-ark-input-with-prefix>
</div>
