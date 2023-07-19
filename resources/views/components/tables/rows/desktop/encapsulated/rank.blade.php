@props([
    'index',
    'results'
])

<span class="text-sm font-semibold leading-4.25">
    {{ $index + ($results->currentPage() - 1) * $results->perPage() }}
</span>
