@props(['exchange'])

<div {{ $attributes->class(['flex divide-x divide-theme-secondary-300 dark:divide-theme-secondary-800 space-x-2 text-theme-secondary-900 dark:text-theme-secondary-200 font-semibold']) }}>
    @foreach ($exchange['pairs'] as $pair)
        <div class="pl-2 first:pl-0">
            {{ $pair }}
        </div>
    @endforeach
</div>
