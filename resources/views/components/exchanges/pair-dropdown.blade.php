<x-exchanges.dropdown
    :title="trans('pages.exchanges.pair.title')"
    icon="app-pair"
>
    <x-general.dropdown.list-item>
        @lang('general.all')
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item>
        @lang('pages.exchanges.pair.btc')
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item>
        @lang('pages.exchanges.pair.eth')
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item>
        @lang('pages.exchanges.pair.stablecoins')
    </x-general.dropdown.list-item>
</x-exchanges.dropdown>
