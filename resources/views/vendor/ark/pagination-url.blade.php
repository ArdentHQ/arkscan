@php
    ['pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\Foundation\UserInterface\UI::getPaginationData(
        $paginator,
    );
@endphp
<div x-data="Pagination('{{ $pageName }}', {{ $paginator->lastPage() }})" class="pagination-wrapper">
    <div class="pagination-pages-mobile relative">
        <form x-show="search" name="searchForm" type="get"
            class="absolute left-0 z-10 flex h-full w-full overflow-hidden rounded bg-theme-primary-100 px-2 dark:bg-theme-secondary-800">
            <input x-model.number="page" type="number" min="1" max="{{ $paginator->lastPage() }}"
                name="{{ $pageName }}" placeholder="@lang ('ui::actions.enter_the_page')"
                class="w-full bg-transparent px-3 py-2 dark:text-theme-secondary-200" x-on:blur="blurHandler" />
            @foreach ($urlParams as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            @endforeach
            <button type="submit"
                class="transition-default p-2 text-theme-secondary-500 hover:text-theme-primary-500 dark:text-theme-secondary-200"
                :disabled="!page">
                <x-ark-icon name="magnifying-glass" size="sm" />
            </button>
            <button type="button"
                class="transition-default p-2 text-theme-secondary-500 hover:text-theme-primary-500 dark:text-theme-secondary-200"
                x-on:click="hideSearch()">
                <x-ark-icon name="cross" size="sm" />
            </button>
        </form>

        <button type="button" class="button-pagination-page-indicator button-pagination-page-indicator--search"
            :class="{ 'opacity-0': search }" x-on:click="toggleSearch">
            <span>
                @lang('ui::generic.pagination.current_to', [
                    'currentPage' => $paginator->currentPage(),
                    'lastPage' => $paginator->lastPage(),
                ])
            </span></button>
    </div>

    <div class="flex space-x-3">
        @if ($paginator->onFirstPage())
            <div class="button-generic button-disabled flex items-center">
                <span class="flex items-center">
                    <x-ark-icon name="arrows.double-chevron-left" size="xs" />
                </span>
            </div>
        @else
            <a class="flex" href="{{ $paginator->url(1) }}">
                <div class="button-secondary pagination-button-mobile flex h-full items-center">
                    <div class="flex items-center">
                        <x-ark-icon name="arrows.double-chevron-left" size="xs" />
                    </div>
                </div>
            </a>
        @endif

        @if ($paginator->onFirstPage())
            <div class="button-generic button-disabled flex items-center">
                <div class="flex items-center">
                    <span class="hidden lg:ml-2 lg:flex">@lang('ui::generic.previous')</span>
                    <x-ark-icon class="inline-block lg:hidden" name="arrows.chevron-left" size="xs" />
                </div>
            </div>
        @else
            <a class="flex" href="{{ $paginator->previousPageUrl() }}">
                <div class="button-secondary pagination-button-mobile flex h-full items-center">
                    <div class="flex items-center">
                        <span class="hidden lg:ml-2 lg:flex">@lang('ui::generic.previous')</span>
                        <x-ark-icon class="inline-block lg:hidden" name="arrows.chevron-left" size="xs" />
                    </div>
                </div>
            </a>
        @endif

        <div class="relative">
            <form x-cloak x-show="search" name="searchForm" type="get"
                class="pagination-form-desktop absolute left-0 z-10 flex h-full w-full overflow-hidden rounded bg-theme-primary-100 px-2 dark:bg-theme-secondary-800">
                <input x-ref="search" x-model.number="page" type="number" min="1"
                    max="{{ $paginator->lastPage() }}" name="{{ $pageName }}"
                    placeholder="@lang ('ui::actions.enter_the_page_number')"
                    class="w-full bg-transparent px-3 py-2 dark:text-theme-secondary-200" x-on:blur="blurHandler" />
                @foreach ($urlParams as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endforeach
                <button type="submit"
                    class="transition-default p-2 text-theme-secondary-500 hover:text-theme-primary-500 dark:text-theme-secondary-200"
                    :disabled="!page">
                    <x-ark-icon name="magnifying-glass" size="sm" />
                </button>
                <button type="button"
                    class="transition-default p-2 text-theme-secondary-500 hover:text-theme-primary-500 dark:text-theme-secondary-200"
                    x-on:click="hideSearch">
                    <x-ark-icon name="cross" size="sm" />
                </button>
            </form>

            <div class="flex-inline hidden rounded bg-theme-primary-100 px-2 dark:bg-theme-secondary-800 md:flex">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <button x-on:click="toggleSearch" type="button"
                            class="button-pagination-page-indicator button-pagination-page-indicator--search"
                            :class="{ 'opacity-0': search }">
                            <span class="button-pagination-search"><x-ark-icon name="magnifying-glass"
                                    size="sm" /></span>
                            <span class="button-pagination-ellipsis">{{ $element }}</span>
                        </button>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <a href="{{ $url }}"
                                class="@if ($paginator->currentPage() === $page) button-pagination-page-indicator--selected @else button-pagination-page-indicator @endif">
                                {{ $page }}
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </div>

            <div class="pagination-pages md:hidden">
                <button x-on:click="toggleSearch" type="button"
                    class="button-pagination-page-indicator button-pagination-page-indicator--search"
                    :class="{ 'opacity-0': search }">
                    <span>
                        @lang('ui::generic.pagination.current_to', [
                            'currentPage' => $paginator->currentPage(),
                            'lastPage' => $paginator->lastPage(),
                        ])
                    </span>
                </button>
            </div>
        </div>

        @if ($paginator->hasMorePages())
            <a class="flex" href="{{ $paginator->nextPageUrl() }}">
                <div class="button-secondary pagination-button-mobile flex h-full items-center">
                    <div class="flex items-center">
                        <span class="hidden lg:mr-2 lg:flex">@lang('ui::generic.next')</span>
                        <x-ark-icon class="inline-block lg:hidden" name="arrows.chevron-right" size="xs" />
                    </div>
                </div>
            </a>
        @else
            <div class="button-generic button-disabled flex items-center">
                <div class="flex items-center">
                    <span class="hidden lg:flex">@lang('ui::generic.next')</span>
                    <x-ark-icon class="inline-block lg:hidden" name="arrows.chevron-right" size="xs" />
                </div>
            </div>
        @endif

        @if ($paginator->hasMorePages())
            <a class="flex" href="{{ $paginator->url($paginator->lastPage()) }}">
                <div class="button-secondary pagination-button-mobile flex h-full items-center">
                    <span class="flex items-center">
                        <x-ark-icon name="arrows.double-chevron-right" size="xs" />
                    </span>
                </div>
            </a>
        @else
            <div class="button-generic button-disabled flex items-center">
                <span class="flex items-center">
                    <x-ark-icon name="arrows.double-chevron-right" size="xs" />
                </span>
            </div>
        @endif
    </div>
</div>
