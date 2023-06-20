@props(['results'])

@if ($results->total() >= config('arkscan.pagination.min_items'))
    <div {{ $attributes->class('-mx-6 px-6 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:border-t-0 md:border rounded-b-xl pt-4 md:pb-4 md:mx-0 flex flex-col items-center space-y-6 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between') }}>
        <div class="flex items-center space-x-2 text-sm font-semibold sm:mr-8 dark:text-theme-secondary-500">
            <span>@lang('pagination.show')</span>

            <x-general.pagination.show-dropdown />

            <span>@lang('pagination.records')</span>
        </div>

        <div class="flex w-full sm:w-auto">
            {{ $results->links('components.general.pagination.includes.simple') }}
        </div>
    </div>
@endif
