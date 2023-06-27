@props([
    'index',
    'results',
])

<div class="font-semibold dark:text-theme-secondary-200">
    {{ $index + ($results->currentPage() - 1) * $results->perPage()  }}
</div>
