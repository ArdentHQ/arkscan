@props([
    'paginator',
])

@php
    ['pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\Foundation\UserInterface\UI::getPaginationData($paginator);

    $urlParams['perPage'] = $paginator->perPage();
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
        <div class="flex overflow-hidden flex-1 items-center px-2 bg-white rounded outline outline-2 outline-theme-primary-600 dark:bg-theme-secondary-900">
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
                class="py-2 px-3 w-full bg-transparent placeholder:dark:text-theme-secondary-700 dark:text-theme-secondary-200"
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
        class="inline-flex justify-center items-center p-0 w-full leading-5 button-secondary focus:ring-theme-primary-300"
        :class="{ 'opacity-0': search }"
        @unless ($paginator->hasPages())
            disabled
        @endunless
    >
        <div class="py-1.5 px-2 sm:px-3 md:px-4">
            <span class="sm:hidden md:inline">
                @lang('ui::generic.pagination.current_to', [
                    'currentPage' => number_format($paginator->currentPage(), 0),
                    'lastPage' => number_format($paginator->lastPage(), 0),
                ])
            </span>

            <span class="hidden sm:block md:hidden">
                @lang('ui::generic.pagination.current_to_short', [
                    'currentPage' => number_format($paginator->currentPage(), 0),
                    'lastPage' => number_format($paginator->lastPage(), 0),
                ])
            </span>
        </div>
    </button>
</div>
