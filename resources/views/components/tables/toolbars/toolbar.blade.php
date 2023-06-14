@props(['resultCount'])

<div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between md:rounded-t-xl md:border md:border-b-0 md:border-theme-secondary-300 md:dark:border-theme-secondary-800 pt-1 pb-4 md:pt-4 md:px-6">
    <div class="font-semibold dark:text-theme-secondary-500">
        @lang('pagination.showing_x_results', ['count' => number_format($resultCount, 0)])
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
