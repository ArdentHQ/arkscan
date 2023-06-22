@props([
    'index',
    'results',
])

<div>
    <span class="font-semibold">
        @lang('labels.rank')
    </span>

    <span class="font-semibold">
        {{ $index + ($results->currentPage() - 1) * $results->perPage()  }}
    </span>
</div>
