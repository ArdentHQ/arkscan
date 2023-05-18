@props(['results'])

@if($results->hasPages())
    <div {{ $attributes->class('-mx-6 px-6 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:border-t-0 md:border rounded-b-xl py-4 md:mx-0 flex flex-col items-center space-y-6 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between sm:space-x-8') }}>
        <div class="flex items-center space-x-2">
            <span>@lang('pagination.show')</span>

            <x-general.pagination.show-dropdown />

            <span>@lang('pagination.records')</span>
        </div>

        <div class="flex w-full">
            {{ $results->links('components.general.pagination.includes.simple', ['class' => 'w-full']) }}
        </div>
    </div>
@endif
