<div class="rounded border border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex justify-between items-center py-3 px-4 rounded-t dark:rounded-t-sm bg-theme-secondary-100 dark:bg-theme-secondary-800">
        {{ $header }}
    </div>

    <div class="flex flex-col px-4 pt-3 pb-4 space-y-4 sm:flex-row sm:space-y-0">
        {{ $slot }}
    </div>
</div>
