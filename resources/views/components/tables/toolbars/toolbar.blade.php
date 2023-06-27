@props(['resultCount'])

<div @class([
    "flex flex-col pt-1 space-y-3 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 md:px-6  md:rounded-t-xl md:border md:border-b-0 md:border-theme-secondary-300 md:dark:border-theme-secondary-800",
    "pb-4 md:pt-4" => $slot->isNotEmpty(),
    "pb-5 md:pt-5" => !$slot->isNotEmpty(),
])>
    <div class="font-semibold dark:text-theme-secondary-500">
        @lang('pagination.showing_x_results', ['count' => number_format($resultCount, 0)])
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
