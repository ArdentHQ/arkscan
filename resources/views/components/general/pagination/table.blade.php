@if($results->hasPages())
    <div class="-mx-6 px-6 border-t border-theme-secondary-300 md:border-t-0 md:border rounded-b-xl py-4 md:mx-0 flex flex-col items-center space-y-6 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between {{ $class ?? '' }}">
        <div class="flex items-center space-x-2">
            <span>@lang('pagination.show')</span>

            <x-general.pagination.show-dropdown />

            <span>@lang('pagination.records')</span>
        </div>

        {{ $results->links('components.general.pagination.includes.simple') }}
    </div>
@endif
