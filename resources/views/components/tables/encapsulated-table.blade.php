@props([
    'rounded' => true,
    'paginator' => null,
    'noResultsMessage' => null,
])

@php ($paginatorIsEmpty = $paginator === null || ($paginator !== null && $paginator->total() < config('arkscan.pagination.per_page')) || (isset($this) && property_exists($this, 'isReady') && ! $this->isReady))

<div {{ $attributes->class([
    'border border-theme-secondary-300 dark:border-theme-secondary-800 overflow-hidden table-container px-6 table-encapsulated encapsulated-table-header-gradient',
    'rounded-t-xl' => $rounded,
    'rounded-b-xl' => $paginatorIsEmpty,
]) }}>
    <table>
        {{ $slot }}
    </table>

    @if ($noResultsMessage)
        <div class="py-4 px-6 text-center">
            {{ $noResultsMessage }}
        </div>
    @endif

    @if ($paginatorIsEmpty)
        <div class="-mx-6 h-[5px] bg-theme-secondary-300 dark:bg-theme-secondary-800"></div>
    @endif
</div>
