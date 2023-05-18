@php
    ['pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\Foundation\UserInterface\UI::getPaginationData($paginator);

    $pageRange = trans('ui::generic.pagination.current_to', [
        'currentPage' => $paginator->currentPage(),
        'lastPage' => number_format($paginator->lastPage(), 0),
    ]);

    $pageRangeShort = trans('ui::generic.pagination.current_to_short', [
        'currentPage' => $paginator->currentPage(),
        'lastPage' => number_format($paginator->lastPage(), 0),
    ]);
@endphp

<div
    x-data="Pagination('{{ $pageName }}', {{ $paginator->lastPage() }})"
    class="pagination-wrapper"
>
    <div class="flex space-x-2">
        <x-general.pagination.includes.arrow
            :page="1"
            icon="arrows.double-chevron-left"
            :text="trans('pagination.first')"
            :disabled="$paginator->onFirstPage()"
        />

        <x-general.pagination.includes.arrow
            :page="$paginator->currentPage() - 1"
            icon="arrows.chevron-left"
            :disabled="$paginator->onFirstPage()"
        />

        {{-- Middle --}}
        <div class="relative">
            <form
                x-show="search"
                name="searchForm"
                type="get"
                class="flex overflow-hidden absolute left-0 z-10 px-2 w-full h-full rounded bg-theme-primary-100 pagination-form-desktop dark:bg-theme-secondary-800"
            >
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

                @foreach($urlParams as $key => $value)
                    <input
                        type="hidden"
                        name="{{ $key }}"
                        value="{{ $value }}"
                    />
                @endforeach

                <button
                    type="submit"
                    class="p-2 text-theme-secondary-500 transition-default dark:text-theme-secondary-200 hover:text-theme-primary-500"
                    :disabled="!page"
                >
                    <x-ark-icon
                        name="magnifying-glass"
                        size="sm"
                    />
                </button>

                <button
                    type="button"
                    class="p-2 text-theme-secondary-500 transition-default dark:text-theme-secondary-200 hover:text-theme-primary-500"
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
                class="button-secondary inline-flex items-center py-1.5 px-2 leading-5"
                :class="{ 'opacity-0': search }"
            >
                <span class="hidden md:inline">
                    {{ $pageRange }}
                </span>

                <span class="md:hidden">
                    {{ $pageRangeShort }}
                </span>
            </button>
        </div>

        <x-general.pagination.includes.arrow
            :page="$paginator->currentPage() + 1"
            icon="arrows.chevron-right"
            :disabled="$paginator->currentPage() === $paginator->lastPage()"
        />

        <x-general.pagination.includes.arrow
            :page="$paginator->lastPage()"
            icon="arrows.double-chevron-right"
            :text="trans('pagination.last')"
            :disabled="$paginator->currentPage() === $paginator->lastPage()"
        />
    </div>
</div>
