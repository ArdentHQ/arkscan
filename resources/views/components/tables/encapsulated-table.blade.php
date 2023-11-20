@props([
    'rounded' => true,
    'paginator' => null,
    'noResultsMessage' => null,
    'withPagination' => false,
])

@php
    $paginatorIsEmpty = false;
    if (! $withPagination) {
        if ($paginator === null) {
            $paginatorIsEmpty = true;
        }

        if ($paginator !== null && $paginator->total() < config('arkscan.pagination.per_page')) {
            $paginatorIsEmpty = true;
        }

        if (isset($this) && property_exists($this, 'isReady') && ! $this->isReady) {
            $paginatorIsEmpty = true;
        }
    }
@endphp

<div {{ $attributes->class([
    'border border-theme-secondary-300 dark:border-theme-dark-800 overflow-hidden',
    'rounded-t-xl' => $rounded,
    'rounded-b-xl' => $paginatorIsEmpty,
]) }}>
    <div class="px-6 table-container table-encapsulated encapsulated-table-header-gradient">
        <table>
            {{ $slot }}
        </table>

        @if ($noResultsMessage)
            <div class="py-4 px-6 text-center">
                {{ $noResultsMessage }}
            </div>
        @endif

        @if ($paginatorIsEmpty)
            <div class="-mx-6 h-[5px] bg-theme-secondary-300 dark:bg-theme-dark-800"></div>
        @endif
    </div>
</div>
