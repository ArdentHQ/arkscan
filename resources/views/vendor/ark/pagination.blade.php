@php
['path' => $path, 'pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\UserInterface\UI::getPaginationData($paginator);
@endphp
<div
    x-data="Pagination('{{ $pageName }}', {{ $paginator->lastPage() }})"
    x-init="init"
    class="pagination-wrapper"
>
    <div class="relative pagination-pages-mobile">
        <form x-show="search" name="searchForm" type="get" action="{{ $path }}" class="flex overflow-hidden absolute left-0 z-10 px-2 w-full h-full rounded bg-theme-primary-100 dark:bg-theme-secondary-800">
            <input
                x-model.number="page"
                type="number"
                min="1"
                max="{{ $paginator->lastPage() }}"
                name="{{ $pageName }}"
                placeholder="Enter the page"
                class="py-2 px-3 w-full bg-transparent dark:text-theme-secondary-200"
                x-on:blur="blurHandler"
            />
            @foreach($urlParams as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            @endforeach
            <button type="submit" class="p-2 text-theme-secondary-500 hover:text-theme-primary-500 transition-default dark:text-theme-secondary-200" :disabled="!page">
                <x-ark-icon name="search" size="sm" />
            </button>
            <button type="button" class="p-2 text-theme-secondary-500 hover:text-theme-primary-500 transition-default dark:text-theme-secondary-200" x-on:click="hideSearch()">
                <x-ark-icon name="close" size="sm" />
            </button>
        </form>

        <button type="button"
            class="button-pagination-page-indicator button-pagination-page-indicator--search"
            :class="{ 'opacity-0': search }"
            x-on:click="toggleSearch"
        ><span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span></button>
    </div>

    <div class="flex space-x-3">
        <button
            wire:click="gotoPage(1)"
            x-on:click="hideSearch"
            class="items-center button-secondary pagination-button-mobile" @if($paginator->onFirstPage()) disabled @endif
        >
            <x-ark-icon name="pagination-first" size="xs" />
        </button>
        <button
            wire:click="gotoPage({{ $paginator->currentPage() - 1 }})"
            x-on:click="hideSearch"
            class="items-center button-secondary pagination-button-mobile" @if($paginator->onFirstPage()) disabled @endif
        >
            <div class="flex items-center">
                <x-ark-icon class="inline-block lg:hidden" name="chevron-left" size="xs" />
                <span class="hidden lg:flex">Previous</span>
            </div>
        </button>

        <div class="relative">
            <form x-show="search" name="searchForm" type="get" action="{{ $path }}" class="flex overflow-hidden absolute left-0 z-10 px-2 w-full h-full rounded bg-theme-primary-100 dark:bg-theme-secondary-800 pagination-form-desktop">
                <input
                    x-ref="search"
                    x-model.number="page"
                    type="number"
                    min="1"
                    max="{{ $paginator->lastPage() }}"
                    name="{{ $pageName }}"
                    placeholder="Enter the page number"
                    class="py-2 px-3 w-full bg-transparent dark:text-theme-secondary-200"
                    x-on:blur="blurHandler"
                />
                @foreach($urlParams as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endforeach
                <button type="submit" class="p-2 text-theme-secondary-500 hover:text-theme-primary-500 transition-default dark:text-theme-secondary-200" :disabled="!page">
                    <x-ark-icon name="search" size="sm" />
                </button>
                <button type="button" class="p-2 text-theme-secondary-500 hover:text-theme-primary-500 transition-default dark:text-theme-secondary-200" x-on:click="hideSearch">
                    <x-ark-icon name="close" size="sm" />
                </button>
            </form>

            <div class="hidden px-2 rounded bg-theme-primary-100 dark:bg-theme-secondary-800 md:flex flex-inline">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <button
                            x-on:click="toggleSearch"
                            type="button"
                            class="button-pagination-page-indicator button-pagination-page-indicator--search"
                            :class="{ 'opacity-0': search }"
                        >
                            <span class="button-pagination-search"><x-ark-icon name="search" size="sm" /></span>
                            <span class="button-pagination-ellipsis">{{ $element }}</span>
                        </button>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <button
                                class="@if ($paginator->currentPage() === $page) button-pagination-page-indicator--selected @else button-pagination-page-indicator  @endif"
                                wire:click="gotoPage({{ $page }})"
                            >
                                {{ $page }}
                            </button>
                        @endforeach
                    @endif
                @endforeach
            </div>

            <div class="pagination-pages md:hidden">
                <button
                    x-on:click="toggleSearch"
                    type="button"
                    class="button-pagination-page-indicator button-pagination-page-indicator--search"
                    :class="{ 'opacity-0': search }"
                ><span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span></button>
            </div>
        </div>

        <button
            wire:click="gotoPage({{ $paginator->currentPage() + 1 }})"
            x-on:click="hideSearch"
            class="items-center button-secondary pagination-button-mobile" @if($paginator->currentPage() === $paginator->lastPage()) disabled @endif
        >
            <div class="flex items-center">
                <span class="hidden lg:flex">Next</span>
                <x-ark-icon class="inline-block lg:hidden" name="chevron-right" size="xs" />
            </div>
        </button>
        <button
            wire:click="gotoPage({{ $paginator->lastPage() }})"
            x-on:click="hideSearch"
            class="items-center button-secondary pagination-button-mobile"
            @if($paginator->currentPage() === $paginator->lastPage()) disabled @endif
        >
            <x-ark-icon name="pagination-last" size="xs" />
        </button>
    </div>
</div>
