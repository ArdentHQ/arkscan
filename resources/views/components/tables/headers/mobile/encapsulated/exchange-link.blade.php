@props(['exchange'])

<a
    href="{{ $exchange->url }}"
    class="flex flex-1 justify-between items-center py-3 px-4"
    target="_blank"
    rel="noopener nofollow noreferrer"
>
    <div class="flex items-center space-x-2">
        <div class="flex justify-center items-center p-1.5 w-8 h-8 bg-white rounded-full border border-theme-secondary-200 dark:border-theme-dark-900 dark:bg-theme-dark-900">
            <img class="max-w-full max-h-full" src="{{ config('arkscan.exchanges.icon_url') }}{{ $exchange->icon }}.svg" alt="{{ $exchange->name }} icon" />
        </div>

        <span class="text-sm font-semibold leading-4 text-theme-primary-600 dark:text-theme-dark-200">{{ $exchange->name }}</span>
    </div>

    <x-ark-icon
        name="arrows.arrow-external"
        size="sm"
        class="text-theme-secondary-500"
    />
</a>
