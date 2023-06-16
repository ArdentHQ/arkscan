@props([
    'index',
    'results'
])

<span class="font-semibold text-sm leading-[17px]">{{ $index + ($results->currentPage() - 1) * $results->perPage()  }}</span>
