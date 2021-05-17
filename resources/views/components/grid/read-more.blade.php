@props([
    'icon',
    'title',
    'content',
])

<x-grid.generic
    :title="$title"
    :icon="$icon"
    class="transition-none"
>
    <x-ark-read-more :content="$content" />
</x-grid.generic>
