@props([
    'navigation' => [],
])

<div class="z-30 md:sticky md:top-0">
    <x-navbar.top />
    <x-navbar.desktop :navigation="$navigation" />
    <x-navbar.mobile :navigation="$navigation" />
</div>
