@props([
    'paginator',
])

@php
    ['pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\Foundation\UserInterface\UI::getPaginationData($paginator);
@endphp

<div {{ $attributes }}>
    <form
        x-show="search"
        name="searchForm"
        type="get"
        class="flex absolute left-0 z-10 space-x-2 w-full h-full bg-white dark:bg-theme-secondary-900"
        x-transition.opacity
        x-cloak
    >
        <div class="flex overflow-hidden flex-1 items-center px-2 rounded bg-white dark:bg-theme-secondary-800 outline outline-2 outline-theme-primary-600">
            <x-ark-icon
                name="magnifying-glass"
                class="text-theme-secondary-500 dark:text-theme-secondary-700"
            />

            <input
                x-ref="search"
                x-model.number="page"
                type="number"
                min="1"
                max="{{ $paginator->lastPage() }}"
                name="{{ $pageName }}"
                placeholder="@lang ('ui::actions.enter_the_page_number')"
                class="py-2 px-3 w-full bg-transparent dark:text-theme-secondary-200"
                x-on:blur="blurHandler"
            />

            <x-ark-icon
                name="square-return-arrow"
                class="hidden sm:block dark:text-theme-secondary-600"
                size="sm"
            />

            @foreach($urlParams as $key => $value)
                <input
                    type="hidden"
                    name="{{ $key }}"
                    value="{{ $value }}"
                />
            @endforeach
        </div>

        <button
            type="button"
            class="p-2 button-secondary"
            x-on:click="hideSearch"
        >
            <x-ark-icon
                name="cross"
                size="sm"
            />
        </button>
    </form>

    <button
        x-on:click="toggleSearch"
        type="button"
        class="inline-flex justify-center items-center py-1.5 px-2 w-full leading-5 md:px-4 button-secondary"
        :class="{ 'opacity-0': search }"
        @unless ($paginator->hasPages())
            disabled
        @endunless
    >
        <span class="hidden md:inline">
            @lang('ui::generic.pagination.current_to', [
                'currentPage' => $paginator->currentPage(),
                'lastPage' => number_format($paginator->lastPage(), 0),
            ])
        </span>

        <span class="md:hidden">
            @lang('ui::generic.pagination.current_to_short', [
                'currentPage' => $paginator->currentPage(),
                'lastPage' => number_format($paginator->lastPage(), 0),
            ])
        </span>
    </button>
</div>
