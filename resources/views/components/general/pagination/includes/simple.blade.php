@props([
    'class' => null,
    'disabled' => false,
])

@php
    ['pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\Foundation\UserInterface\UI::getPaginationData($paginator);
@endphp

<div
    x-data="Pagination('{{ $pageName }}', {{ $paginator->lastPage() }})"
    @class(['relative pagination-wrapper flex justify-between flex-col sm:flex-row w-full sm:w-auto', $class])
>
    <x-general.pagination.includes.middle
        class="flex relative mb-2 w-full sm:hidden"
        :paginator="$paginator"
        :disabled="$disabled"
    />

    <div class="flex space-x-2 w-full sm:w-auto">
        <x-general.pagination.includes.arrow
            :page="1"
            icon="arrows.double-chevron-left-small"
            :text="trans('pagination.first')"
            :disabled="$disabled || $paginator->onFirstPage()"
        />

        <x-general.pagination.includes.arrow
            :page="$paginator->currentPage() - 1"
            icon="arrows.chevron-left-small"
            :disabled="$disabled || $paginator->onFirstPage()"
        />

        <x-general.pagination.includes.middle
            class="hidden sm:block"
            ::class="{ 'w-full max-w-[346px]': search }"
            :paginator="$paginator"
            :disabled="$disabled"
        />

        <x-general.pagination.includes.arrow
            :page="$paginator->currentPage() + 1"
            icon="arrows.chevron-right-small"
            :disabled="$disabled || $paginator->currentPage() === $paginator->lastPage()"
        />

        <x-general.pagination.includes.arrow
            :page="$paginator->lastPage()"
            icon="arrows.double-chevron-right-small"
            :text="trans('pagination.last')"
            :disabled="$disabled || $paginator->currentPage() === $paginator->lastPage()"
        />
    </div>
</div>
