@props([
    'index',
    'results'
])

<span class="text-sm font-semibold leading-[17px]">{{ $index + ($results->currentPage() - 1) * $results->perPage()  }}</span>
