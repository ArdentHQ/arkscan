@props([
    'noResultsMessage' => null,
])

<div {{ $attributes->class('flex flex-col space-y-3 table-list-mobile table-list-encapsulated') }}>
    @unless ($noResultsMessage)
        {{ $slot }}
    @else
        <div class="dark:text-theme-dark-500">
            {{ $noResultsMessage }}
        </div>
    @endunless
</div>
