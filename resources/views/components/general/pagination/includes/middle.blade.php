@props([
    'paginator',
    'disabled' => false,
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
        class="flex absolute left-0 z-10 space-x-2 w-full h-full bg-white dark:bg-theme-dark-900"
        x-transition.opacity
        x-cloak
        x-on:submit="(e) => {
            e.preventDefault();
            $wire.setPage($refs.search.value);
            search = false;
        }"
    >
        <div class="flex overflow-hidden flex-1 items-center px-2 bg-white rounded outline outline-2 outline-theme-primary-600 dark:bg-theme-dark-900">
            <x-ark-icon
                name="magnifying-glass"
                class="text-theme-secondary-500 dark:text-theme-dark-700"
            />

            <input
                x-ref="search"
                x-model.number="page"
                type="number"
                min="1"
                max="{{ $paginator->lastPage() }}"
                name="{{ $pageName }}"
                placeholder="@lang ('ui::actions.enter_the_page_number')"
                class="py-2 px-3 w-full bg-transparent placeholder:dark:text-theme-dark-700 dark:text-theme-dark-200"
                x-on:blur="blurHandler"
            />

            <x-ark-icon
                name="square-return-arrow"
                class="hidden sm:block dark:text-theme-dark-600"
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
        class="inline-flex relative justify-center items-center p-0 w-full leading-5 button-secondary group/pagination focus:ring-theme-primary-500 focus:dark:ring-theme-dark-blue-300"
        :class="{ 'opacity-0': search }"
        @if ($disabled || ! $paginator->hasPages())
            disabled
        @endif
    >
        <div class="py-1.5 px-2 sm:px-3 md:px-4 group-hover/pagination:text-transparent">
            @lang('ui::generic.pagination.current_to', [
                'currentPage' => number_format($paginator->currentPage(), 0),
                'lastPage' => number_format($paginator->lastPage(), 0),
            ])
        </div>

        <div class="absolute m-auto text-transparent group-hover/pagination:text-white">
            <x-ark-icon
                name="magnifying-glass-small"
                size="sm"
            />
        </div>
    </button>
</div>
