@props([
    'index',
    'results'
])

<span class="font-semibold">{{ $index + ($results->currentPage() - 1) * $results->perPage()  }}</span>
