@props([
    'class' => null,
])

@php
    ['pageName' => $pageName, 'urlParams' => $urlParams] = ARKEcosystem\Foundation\UserInterface\UI::getPaginationData($paginator);
@endphp

<div
    x-data="Pagination('{{ $pageName }}', {{ $paginator->lastPage() }})"
    @class(['relative pagination-wrapper flex justify-between flex-col sm:flex-row w-full sm:w-auto', $class])
>
    <x-general.pagination.includes.middle
        class="flex mb-2 w-full sm:hidden relative"
        :paginator="$paginator"
    />

    <div class="flex space-x-2 w-full sm:w-auto">
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

        <x-general.pagination.includes.middle
            class="hidden sm:block"
            ::class="{ 'w-full max-w-[346px]': search }"
            :paginator="$paginator"
        />

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
