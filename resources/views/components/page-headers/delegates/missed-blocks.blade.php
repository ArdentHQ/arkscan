<x-page-headers.delegates.header-item
    title="Missed Blocks (30 Days)"
    :attributes="$attributes"
>
    <div class="flex space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
        <div class="flex items-center space-x-2">
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">17</span>

            <x-general.badge class="py-px">
                3 Delegates
            </x-general.badge>
        </div>

        <a
            class="pl-3 link text-sm md:text-base !leading-5"
            href="#"
        >
            @lang('actions.view')
        </a>
    </div>
</x-delegates.header-item>
