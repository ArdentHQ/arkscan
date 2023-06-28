@props(['block'])

<x-search.results.result :model="$block">
    <div class="flex items-center space-x-2">
        <div class="dark:text-theme-secondary-500">@lang('general.search.block')</div>

        <div class="min-w-0 link group-hover/result:no-underline hover:text-theme-primary-600">
            <x-truncate-dynamic>
                {{ $block->id() }}
            </x-truncate-dynamic>
        </div>
    </div>

    <div class="flex flex-col space-y-2 md:flex-row md:items-center md:space-y-0 md:space-x-4">
        <div class="flex items-center space-x-2 text-xs isolate">
            <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('general.search.generated_by')
            </div>

            <x-general.identity
                :model="$block->delegate()"
                without-reverse
                without-truncate
                without-reverse-class="space-x-2"
                without-link
                without-icon
                :link-wallet="false"
                class="text-theme-secondary-700 dark:text-theme-secondary-500"
            />
        </div>

        <div class="flex items-center space-x-1 text-xs">
            <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('general.search.transactions')
            </div>

            <div class="text-theme-secondary-700 dark:text-theme-secondary-500">
                {{ $block->transactionCount() }}
            </div>
        </div>
    </div>
</x-search.results.result>
