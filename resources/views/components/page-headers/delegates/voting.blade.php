<x-page-headers.delegates.header-item
    :title="trans('pages.delegates.voting_x_addresses', ['count' => 123])"
    :attributes="$attributes"
>
    <div class="flex items-center space-x-2">
        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
            <x-currency :currency="Network::currency()">
                {{-- TODO: voted network currency --}}
                {{ 10003 }}
            </x-currency>
        </span>

        <x-general.badge class="py-px">
            {{-- TODO: voted percentage --}}
            <x-percentage>123</x-percentage>
        </x-general.badge>
    </div>
</x-delegates.header-item>
