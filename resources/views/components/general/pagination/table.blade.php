@props([
    'results',
])

@if ($results->total() >= config('arkscan.pagination.per_page'))
    <div {{ $attributes->class('-mx-6 px-6 border-t border-theme-secondary-300 dark:border-theme-dark-800 md:border-t-0 md:border rounded-b-xl mt-4 md:mt-0 pt-4 md:pb-4 md:mx-0 flex flex-col items-center space-y-6 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between') }}>
        <div class="flex items-center space-x-2 text-sm font-semibold sm:mr-8 dark:text-theme-dark-200">
            <span>@lang('pagination.show')</span>

            <div wire:loading.remove>
                <x-general.pagination.show-dropdown />
            </div>

            <div wire:loading>
                <x-general.pagination.show-dropdown disabled />
            </div>

            <span>@lang('pagination.records')</span>
        </div>

        <div
            class="flex w-full sm:w-auto"
            wire:loading.remove
        >
            {{ $results->links('components.general.pagination.includes.simple') }}
        </div>

        <div
            class="flex w-full sm:w-auto"
            wire:loading
        >
            {{ $results->links('components.general.pagination.includes.simple', ['disabled' => true]) }}
        </div>
    </div>
@endif
