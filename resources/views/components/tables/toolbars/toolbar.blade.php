@props(['resultCount'])

<div class="flex flex-col pt-1 pb-4 space-y-3 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 md:px-6 md:pt-4 md:rounded-t-xl md:border md:border-b-0 md:border-theme-secondary-300 md:dark:border-theme-secondary-800">
    <div class="font-semibold dark:text-theme-secondary-500">
        @lang('pagination.showing_x_results', ['count' => number_format($resultCount, 0)])
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
