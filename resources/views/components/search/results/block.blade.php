@props(['block'])

<x-search.results.result :model="$block">
    <x-tables.rows.mobile class="md:hidden">
        <x-slot name="header" class="leading-4.25">
            <div class="min-w-0 link group-hover/result:no-underline hover:text-theme-primary-600">
                <x-truncate-middle>
                    {{ $block->id() }}
                </x-truncate-middle>
            </div>
        </x-slot>

        <div class="flex flex-col space-y-4">
            <x-search.results.mobile.detail :title="trans('general.search.generated_by')">
                <x-general.identity
                    :model="$block->delegate()"
                    without-link
                    :link-wallet="false"
                    class="text-theme-secondary-900 dark:text-theme-dark-50"
                />
            </x-search.results.mobile.detail>

            <x-search.results.mobile.detail :title="trans('general.search.transactions')">
                {{ $block->transactionCount() }}
            </x-search.results.mobile.detail>
        </div>
    </x-tables.rows.mobile>

    <div class="hidden flex-col space-y-2 md:flex">
        <div class="flex items-center space-x-2">
            <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                @lang('general.search.block')
            </div>

            <div class="min-w-0 link group-hover/result:no-underline hover:text-theme-primary-600">
                <x-truncate-middle :length="20">
                    {{ $block->id() }}
                </x-truncate-middle>
            </div>
        </div>

        <div class="flex flex-col space-y-2 md:flex-row md:items-center md:space-y-0 md:space-x-4">
            <div class="flex items-center space-x-2 text-xs isolate">
                <div class="text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('general.search.generated_by')
                </div>

                <x-general.identity
                    :model="$block->delegate()"
                    without-link
                    :link-wallet="false"
                    class="text-theme-secondary-900 dark:text-theme-dark-50"
                />
            </div>

            <div class="flex items-center space-x-1 text-xs">
                <div class="text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('general.search.transactions')
                </div>

                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $block->transactionCount() }}
                </div>
            </div>
        </div>
    </div>
</x-search.results.result>
